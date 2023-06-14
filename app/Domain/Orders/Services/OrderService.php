<?php
namespace App\Domain\Orders\Services;

use App\Domain\Content\Models\Coupon;
use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\MailService;
use App\Domain\Orders\Models\Order;
use App\Domain\Helpers\StatusService;
use App\Domain\Orders\Models\OrderLog;
use App\Events\Orders\OrderCreatedEvent;
use App\Mail\Tests\OrderStatusUpdateMail;
use App\Domain\Users\Services\UserService;
use App\Domain\General\Models\LuContentType;
use App\Domain\Orders\Models\MarketingToken;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Content\Services\ContentService;
use App\Domain\Helpers\DataManipulationService;
use App\Domain\Payment\Services\PaymentService;
use App\Domain\Orders\Services\MarketingTokenService;

class OrderService
{
  /**
   * @var LogService
  */
  private $log_service;

  /**
   * @var UserService|null
  */
  private $user_service;

  /**
   * @var PaymentService|null
  */
  private $payment_service;

  /**
   * @var ContentService|null
  */
  private $content_service;

  /**
   * @var MarketingTokenService|null
  */
  private $marketing_token_service;

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
      $this->log_service->error('The requested content ' . $data['content_id'] . ' is already active or pending');
      throw new Exception('User cannot create this order, there has already an active or pending order of this content'); 
    }

    $coupon           = $data['coupon_code'] ? $this->content_service->getCoupon($data['coupon_code']) : null;
    $marketing_token  = isset($data['marketing_token']) ? $this->marketing_token_service->getMarketingTokenByToken($data['marketing_token']) : null;

    $order                      = new Order();
    $order->user_id             = $created_by;
    $order->content_type_id     = LuContentType::COURSE;
    $order->content_id          = $data['content_id'];
    $order->coupon_id           = $coupon->id ?? null;
    $order->marketing_token_id  = $marketing_token->id ?? null;
    $order->price               = $this->getOrderPrice($course, $coupon, $marketing_token);
    $order->status              = StatusService::PENDING;
    $order->order_number        = $this->generateOrderTicketNumber();
    $order->save();

    $this->log_service->info('Order has been created: ' . json_encode($order));
    $this->startPaymentTransaction($order);
    
    return Order::where('id', $order->id)->value('token');
  }
  
  /**
   * When order is completed we will update the order state
   *
   * @param  mixed $token
   * @return void
  */
  public function completed(string $token)
  {
    $order = Order::where('token', $token)
                  ->select('content_id', 'user_id')
                  ->first();

    if(!$order) {
      $this->log_service->error('Failed to complete the order with the token ' . $token);
      return null;
    }
                  
    $this->user_service->assignCourseToUser($order->user_id, $order->content_id);
    $order->update([
      'status' => StatusService::ACTIVE
    ]);
    $this->log_service->info('Order ' . $order->id . ' has been completed');
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
   * TODO: when adding a new provider need to make it more dynamic
   * 
   * @param Order $order
   * @param string $provider
   * @return bool
  */
  private function startPaymentTransaction(Order $order, string $provider = 'visa'): bool
  {
    try {
      $this->payment_service = new PaymentService($order, $provider);
      $this->payment_service->startTransaction();

      return true;
    } catch(Exception $ex) {
      $this->log_service->critical($ex);
      return false;
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
      $course_discount = ($course->discount / 100) * $course->price;
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
    $this->log_service->info("Calc order price: course_discount=$course_discount|coupon_discount=$coupon_discount|marketing_token_discount=$marketing_token_discount|total_price=$total_price");
    return $total_price;
  }

  /**
   * @param int $content_id
   * @param int $created_by
   * @return boolean
  */
  private function canCreateOrder(int $content_id, int $created_by): bool
  {
    $has_active_order = $this->userHasAValidOrderForThatCourse($content_id, $created_by);
    // add more rules here...

    return !$has_active_order;
  }

  /**
   * @param int $content_id
   * @param int $created_by
   * @return boolean
  */
  private function userHasAValidOrderForThatCourse(int $content_id, int $created_by): bool
  {
    return Order::where('user_id', $created_by)
                ->where('content_id', $content_id)
                ->whereIn('status', [StatusService::ACTIVE, StatusService::PENDING, StatusService::IN_PROGRESS])
                ->exists();
  }
}