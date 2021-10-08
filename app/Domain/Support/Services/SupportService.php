<?php
namespace App\Domain\Support\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\MailService;
use App\Domain\Users\Services\UserService;
use App\Domain\Support\Models\SupportTicket;
use Illuminate\Database\Eloquent\Collection;

class SupportService
{
  /**
   * @var LogService
  */
  private $log_service;

  /**
   * @var SupportCategoryService|null
  */
  private $support_category_service;

  /**
   * @var UserService|null
  */
  private $user_service;

  public function __construct(SupportCategoryService $support_category_service, UserService $user_service = null)
  {
    $this->support_category_service = $support_category_service;
    $this->user_service = $user_service;
    $this->log_service = new LogService('support');
  }
  
  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return SupportTicket::orderBy('created_at', 'desc')
                ->get();
  }
    
  /**
   * @param int $support_id
   * @param int $status
   * @param int $updated_by
   * @return void
  */
  public function updateStatus(int $support_id, int $status, int $updated_by)
  {
    if(!$support = SupportTicket::find($support_id)) {
      throw new Exception('Support not found');
    }

    $support->update(['status' => $status]);
    $support->load('user');
    
    $mail_service = new MailService;
    $mail_service->delay()->send(
      $support->user->email,
      SupportStatusUpdateMail::class,
      $support
    );
  }
}