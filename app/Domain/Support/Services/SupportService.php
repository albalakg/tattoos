<?php
namespace App\Domain\Support\Services;

use Exception;
use App\Domain\Users\Models\User;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Helpers\MailService;
use App\Domain\Helpers\StatusService;
use App\Domain\Users\Services\UserService;
use App\Domain\Support\Models\SupportTicket;
use App\Mail\Tests\SupportTicketMessageMail;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Helpers\DataManipulationService;
use App\Domain\Support\Models\SupportTicketLog;
use App\Domain\Support\Models\SupportTicketMessage;

class SupportService
{
  const SUPPORT_TICKET_MESSAGES_FILES_PATH = 'support/tickets/messages';

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
    $tickets = SupportTicket::query()
                ->with('messages')
                ->orderBy('id', 'desc')
                ->get();

    return $this->convertToHumanTime($tickets);
  }
  
  /**
   * @param array|int $users_ids
   * @return Collection
  */
  public function getTicketsByUsers($users_ids): Collection
  {
    $users_ids = DataManipulationService::intToArray($users_ids);

    $tickets = SupportTicket::query()
                ->whereIn('user_id', $users_ids)
                ->with('messages', 'category')
                ->orderBy('id', 'desc')
                ->select('id', 'support_category_id', 'support_number', 'title', 'description', 'status', 'file_path', 'created_at', 'finished_at')
                ->get();

    foreach($tickets AS $ticket) {
      $ticket->human_time = $ticket->created_at->diffForHumans();
      
      foreach($ticket->messages AS $message) {
        $message->human_time = $message->created_at->diffForHumans();
      }
    }

    return $tickets;
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
      throw new Exception('Support Ticket not found');
    }

    $support->status = $status;
    $support->save();
    $support->load('user');
    
    $mail_service = new MailService;
    $mail_service->delay()->send(
      $support->user->email,
      SupportStatusUpdateMail::class,
      $support
    );

    $this->saveSupportTicketLog($support, $updated_by);
  }
  
  /**
   * @param array $data
   * @param User|null $user_id
   * @return Array
  */
  public function createSupportTicket(array $data, ?User $user): Array
  {
    $support_ticket                       = new SupportTicket;
    $support_ticket->user_id              = $user ? $user->id : null;
    $support_ticket->support_number       = $this->generateSupportTicketNumber();
    $support_ticket->support_category_id  = $data['support_category_id'];
    $support_ticket->title                = $data['title'];
    $support_ticket->file_path            = !empty($data['file']) ? FileService::create($data['file'], self::SUPPORT_TICKET_MESSAGES_FILES_PATH) : null;
    $support_ticket->description          = $data['description'];
    $support_ticket->status               = StatusService::ACTIVE;
    $support_ticket->save();

    return [
      'support_number' => $support_ticket->support_number,
      'title' => $support_ticket->title,
      'created_at' => $support_ticket->created_at,
    ];
  }
    
  /**
   * @param array $data
   * @param int $created_by
   * @return SupportTicketMessage
  */
  public function createSupportTicketMessage(array $data, int $created_by): SupportTicketMessage
  {
    if(!SupportTicket::where('id', $data['support_ticket_id'])->exists()) {
      throw new Exception('Support Ticket not found');
    }

    $support_ticket_message                     = new SupportTicketMessage();
    $support_ticket_message->support_ticket_id  = $data['support_ticket_id'];
    $support_ticket_message->message            = $data['message'];
    $support_ticket_message->created_at         = now();
    $support_ticket_message->created_by         = $created_by;

    if(!empty($data['file_path'])) {
      $support_ticket_message->file_path = FileService::create($data['file_path'], self::SUPPORT_TICKET_MESSAGES_FILES_PATH);
    }

    $support_ticket_message->save();
    $support_ticket_message->load('customer');

    $mail_service = new MailService;
    $mail_service->delay()->send(
      $support_ticket_message->customer->email,
      SupportTicketMessageMail::class,
      $support_ticket_message
    );

    $support_ticket_message->human_time = $support_ticket_message->created_at->diffForHumans();

    return $support_ticket_message; 
  }
  
  /**
   * @param SupportTicket $support_ticket
   * @param int $created_by
   * @return void
  */
  private function saveSupportTicketLog(SupportTicket $support_ticket, int $created_by)
  {
    try {
      $support_ticket_log                     = new SupportTicketLog();
      $support_ticket_log->support_ticket_id  = $support_ticket->id;
      $support_ticket_log->status             = $support_ticket->status;
      $support_ticket_log->created_at         = now();
      $support_ticket_log->created_by         = $created_by;
      $support_ticket_log->save();
    } catch(Exception $ex) {
      $this->log_service->error($ex);
    }
  }
  
  /**
   * @param Collection $tickets
   * @return Collection
  */
  private function convertToHumanTime($tickets): Collection
  {
    foreach($tickets AS $ticket) {
      foreach($ticket->messages AS  $message) {
        $message->human_time = $message->created_at->diffForHumans();
      }
    }

    return $tickets;
  }
  
  /**
   * Generating a unique support number
   *
   * @return string
  */
  private function generateSupportTicketNumber(): string
  {
    $support_ticket_number = 'SN' . random_int(0000000, 9999999);
    return $support_ticket_number;
  }
}