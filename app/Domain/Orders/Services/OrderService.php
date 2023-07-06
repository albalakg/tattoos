<?php
namespace App\Domain\Orders\Services;

use Exception;
use Illuminate\Support\Carbon;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\MailService;
use App\Domain\Orders\Models\Order;
use App\Domain\Content\Models\Coupon;
use App\Domain\Helpers\StatusService;
use App\Domain\Orders\Models\OrderLog;
use App\Mail\User\AddCourseToUserMail;
use App\Mail\Tests\OrderStatusUpdateMail;
use App\Domain\Users\Services\UserService;
use App\Domain\General\Models\LuContentType;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Content\Services\ContentService;
use App\Domain\Helpers\DataManipulationService;
use App\Domain\Payment\Services\PaymentService;
use App\Domain\Orders\Services\MarketingTokenService;

class OrderService
{
  const ORDER_PAGE_EXPIRATION_TIME = 25; // minutes

  private LogService $log_service;

  private UserService|null $user_service;

  private PaymentService|null $payment_service;

  private ContentService|null $content_service;

  private MarketingTokenService|null $marketing_token_service;

  public function __construct(UserService $user_service = null, ContentService $content_service = null, MarketingTokenService $marketing_token_service = null)
  {
    $this->user_service             = $user_service;
    $this->content_service          = $content_service;
    $this->marketing_token_service  = $marketing_token_service;
    $this->log_service              = new LogService('orders');
  }
  
  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return Order::orderBy('id', 'desc')
                ->get();
  }
  
  /**
   * @param array|int $users_ids
   * @return Collection
  */
  public function getOrdersByUsers($users_ids): Collection
  {
    $users_ids = DataManipulationService::intToArray($users_ids);

    return Order::orderBy('id', 'desc')
                ->whereIn('user_id', $users_ids)
                ->select('order_number', 'content_id', 'status', 'price', 'created_at')
                ->get();
  }
    
  /**
   * @param int $order_id
   * @param int $status
   * @param int $updated_by
   * @return void
  */
  public function updateStatus(int $order_id, int $status, int $updated_by)
  {
    if(!$order = Order::find($order_id)) {
      $this->log_service->error('Order ' . $order_id . ' was not found');
      throw new Exception('Order was not found');
    }

    $this->saveOrderLog($order, $updated_by);

    $order->update(['status' => $status]);
    $this->user_service->getUserByID($order->user_id);

    $mail_service = new MailService;
    $mail_service->delay()->send(
      $order->user->email,
      OrderStatusUpdateMail::class,
      $order
    );
  }
  
  /**
   * @param int $user_id
   * @return Order|null
  */
  public function getUserRecentOrder(int $user_id): ?Order
  {
    return Order::where('user_id', $user_id)
                ->where('created_at', '>=', Carbon::now()->subHours(2)->toDateTimeString())
                ->select('order_number', 'status', 'price')
                ->orderBy('created_at', 'desc')
                ->first();
  }
  
  /**
   * @param string $token
   * @param array|null $select
   * @return Order|null
  */
  public function getOrderByToken(string $token, ?array $select = null): ?Order
  {
    $order = Order::where('token', $token);
    if($select) {
      $order = $order->select($select);
    }    
    return $order->first(); 
  }
  
  /**
   * @param int $user_id
   * @param int $content_id
   * @return Order|null
  */
  public function getOrderByUserAndContent(int $user_id, int $content_id): ?Order
  {
    return Order::where('user_id', $user_id)->where('content_id', $content_id)->first(); 
  }
    
  /**
   * @param array $data
   * @param int $created_by
   * @return void
  */
  public function create(array $data, int $created_by)
  {
    $course = null;
    $coupon = null;

    if(!$course = $this->content_service->getCourse($data['content_id'])) {
      $this->log_service->error('The requested content ' . $data['content_id'] . ' was not found');
      throw new Exception('The requested content does not exists');
    }

    if(!$this->canCreateOrder($data['content_id'], $created_by)) {
      $this->log_service->info('The requested content is already active or pending', ['content_id' => $data['content_id']]);
      $order = $this->getOrderByUserAndContent($created_by, $data['content_id']);
      if($order && $current_order_page = $this->getOrderPageLink($order)) {
        return $current_order_page;
      }

      throw new Exception('User cannot create this order, there has already an active or pending order of this content'); 
    }


    $coupon           = $data['coupon_code'] ? $this->content_service->getCoupon($data['coupon_code']) : null;
    $marketing_token  = isset($data['marketing_token']) ? $this->marketing_token_service->getMarketingTokenByToken($data['marketing_token']) : null;

    if($marketing_token && $marketing_token->status !== StatusService::ACTIVE) {
      $this->log_service->info('The marketing token received is inactive', ['id' => $marketing_token->id, 'status' => $marketing_token->status]);
      $marketing_token = null;
    }

    $order                      = new Order();
    $order->user_id             = $created_by;
    $order->content_type_id     = LuContentType::COURSE;
    $order->content_id          = $data['content_id'];
    $order->coupon_id           = $coupon->id ?? null;
    $order->marketing_token_id  = $marketing_token->id ?? null;
    $order->price               = $this->getOrderPrice($course, $coupon, $marketing_token);
    $order->status              = StatusService::IN_PROGRESS;
    $order->order_number        = $this->generateOrderTicketNumber();
    $order->save();

    $this->log_service->info('Order has been created: ' . json_encode($order));

    $order->course    = $course;
    $payment_response = $this->startPaymentTransaction($order);
    $order->token     = $payment_response['token'];
    $order->save();

    return [
      'page_link' => $payment_response['link']
    ];
  }
  
  /**
   * When order is completed we will update the order state
   *
   * @param array $data
   * @return void
  */
  public function orderCompleted(array $data)
  {
    $is_valid = $this->isOrderCallbackValid($data);
    $order    = $this->getOrderByToken($data['page_request_uid']);

    if(!$order) {
      $this->log_service->error('Order not found with token', ['token' => $data['page_request_uid']]);
      return;
    }

    if($is_valid) {
      $this->updateOrderToCompletedSuccessfully($order, $data['approval_number']);
      $mail_service = new MailService;
      $mail_service->send(
        $order->user->email,
        AddCourseToUserMail::class,
        $order
      );
    } else {
      $this->updateOrderToFailed($order);
    }

    $this->log_service->info('Order completed successfully', ['id' => $order->id, 'is_valid' => $is_valid]);
  }
  
  /**
   * @param Order $order
   * @param string $approval_number
   * @return void
  */
  private function updateOrderToCompletedSuccessfully(Order $order, string $approval_number)
  {
    $order->status        = StatusService::ACTIVE;
    $order->approval_num  = $approval_number;
    $order->save();
    
    $this->user_service->assignCourseToUser($order->user_id, $order->content_id);
  }
  
  /**
   * @param Order $order
   * @return void
  */
  private function updateOrderToFailed(Order $order)
  {
    $order->status = StatusService::INACTIVE;
    $order->save();
  }


  /**
   * @param Order $order
   * @param int $created_by
   * @return void
  */
  private function saveOrderLog(Order $order, int $created_by)
  {
    try {
      $order_log              = new OrderLog();
      $order_log->order_id    = $order->id;
      $order_log->status      = $order->status;
      $order_log->created_at  = now();
      $order_log->created_by  = $created_by;
      $order_log->save();

      $this->log_service->info('Order was updated to: ' . json_encode($order_log));
    } catch(Exception $ex) {
      $this->log_service->error($ex);
    }
  }
  
  /**
   * visa is the default provider and at the moment the only provider
   * returns the generated page link
   * TODO: when adding a new provider need to make it more dynamic
   * 
   * @param Order $order
   * @param string $provider
   * @return ?array
  */
  private function startPaymentTransaction(Order $order, string $provider = 'visa'): ?array
  {
    try {
      $this->payment_service = new PaymentService($provider);
      return $this->payment_service->startTransaction($order);
    } catch(Exception $ex) {
      $this->log_service->critical($ex);
      return null;
    }
  }
  
  /**
   * Generating a unique order number
   *
   * @return string
  */
  private function generateOrderTicketNumber(): string
  {
    $order_number = 'ON' . random_int(0000000, 9999999);
    if(Order::where('order_number', $order_number)->exists()) {
      return $this->generateOrderTicketNumber();
    }
    return $order_number;
  }
  
  /**
   * Calculate the price of the order
   * The price is built by the content price and discount
   * and also by the coupon that was inserted
   *
   * returns the total order price
   * the value is decimal
   * 
   * @param object $course
   * @param object|null $coupon
   * @param object|null $marketing_token
   * @return void
  */
  private function getOrderPrice(object $course, ?object $coupon = null, ?object $marketing_token = null)
  {
    $course_discount            = 0;
    $coupon_discount            = 0;
    $marketing_token_discount   = 0;
    $taxes                      = 1.17;

    if($course->discount) {
      $course_discount = floor(($course->discount / 100) * $course->price);
    }
    
    if($coupon) {
      $coupon_discount =  $coupon->type === Coupon::TYPE_PERCENTAGE ? 
      ($coupon->value / 100) * $course->price : 
      $coupon->value;
    }
    
    if($marketing_token) {
      $marketing_token_discount = $marketing_token->discount;
    }
    
    $total_price = floor(($course->price - $course_discount - $coupon_discount - $marketing_token_discount) * $taxes);
    $this->log_service->info('Calc order price', [
      'course_price'              => $course->price,
      'course_discount'           => $course_discount,
      'coupon_discount'           => $coupon_discount,
      'marketing_token_discount'  => $marketing_token_discount,
      'total_price'               => $total_price,
    ]);

    return $total_price;
  }

  /**
   * @param int $content_id
   * @param int $user_id
   * @return bool
  */
  private function canCreateOrder(int $content_id, int $user_id): bool
  {
    $is_user_already_assigned = $this->user_service->isUserAssignedToCourse($user_id, $content_id);
    // add more rules here...

    return !$is_user_already_assigned;
  }

  /**
   * @param Order $order
   * @return array|null
  */
  private function getOrderPageLink(Order $order): ?array
  {
    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at);
    return Carbon::now()->diffInMinutes($created_at) > self::ORDER_PAGE_EXPIRATION_TIME ? null : [
      'page_link' => config('payment.payplus.page_address') . $order['token']
    ]; 
  }

  /**
   * @param array $response
   * @return bool
  */
  private function isOrderCallbackValid(array $response): bool
  {
    $this->payment_service = new PaymentService('visa');
    return $this->payment_service->isPaymentCallbackValid($response);
  }
}