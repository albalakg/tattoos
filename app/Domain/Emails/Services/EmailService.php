<?php
namespace App\Domain\Emails\Services;

use App\Domain\Helpers\StatusService;
use App\Domain\Emails\Models\EmailSentUser;
use App\Domain\Emails\Models\EmailsSent;
use App\Domain\Helpers\LogService;
use Exception;

class EmailService
{  
  /**
   * @param array $receivers
   * @param string $email_type
   * @param $data
   * @return EmailsSent|null
  */
  public function create(array $receivers, string $email_type, $data): ?EmailsSent
  {
    try {
      $email_sent = new EmailsSent;
      $email_sent->email_type_id = $email_type::getTypeId();
      $email_sent->parameters = json_encode($data);
      $email_sent->status = StatusService::PENDING;
      $email_sent->created_at = now();
      $email_sent->save();

      $this->addEmailReceivers($email_sent->id, $receivers);

      return $email_sent;
    } catch(Exception $ex) {
      $logger = new LogService('mail');
      $logger->critical($ex);
    }

    return null;
  } 

  /**
   * @param int $id
   * @param int $status
   * @return bool
  */
  public function updateStatus(int $id, int $status): bool
  {
    return EmailsSent::where('id', $id)->update([
      'status' => $status
    ]);
  } 
  
  /**
   * @param int $email_sent_id
   * @param array $receivers
   * @return void
  */
  private function addEmailReceivers(int $email_sent_id, array $receivers)
  {
    $data = [];

    foreach($receivers AS $receiver_email) {
      $data[] = [
        'email_sent_id' => $email_sent_id,
        'email'         => $receiver_email
      ];
    }

    EmailSentUser::insert($data);
  }
}