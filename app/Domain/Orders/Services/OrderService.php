<?php
namespace App\Domain\Orders\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\MailService;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\OrderLog;
use App\Mail\Tests\OrderStatusUpdateMail;
use App\Domain\Users\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Helpers\DataManipulationService;

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

  public function __construct(UserService $user_service = null)
  {
    $this->user_service = $user_service;
    $this->log_service = new LogService('orders');
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
    $users_ids = DataManipulationService::intOrArray($users_ids);

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
      throw new Exception('Order not found');
    }

    $this->saveOrderLog($order, $updated_by);

    $order->update(['status' => $status]);
    $order->load('user');

    $mail_service = new MailService;
    $mail_service->delay()->send(
      $order->user->email,
      OrderStatusUpdateMail::class,
      $order
    );
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
    } catch(Exception $ex) {
      $this->log_service->error($ex);
    }
  }
}