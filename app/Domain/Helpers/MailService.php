<?php

namespace App\Services\Mail;

use Exception;
use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Facades\Mail;
use App\Services\Logger\LoggerService;

class MailService
{
  const MAIL_LOG_DRIVER = 'mail';

  /**
   * @var array
   */
  private $receivers;

  /**
   * @var int
   */
  private $delay;

  /**
   * @var LogService
   */
  private $log_service;

  public function __construct()
  {
    $this->log_service = new LogService('mail');
  }
  
  /**
   * Send the mail in queue
   *
   * @param int $seconds
   * @return self
  */
  public function setDelay(int $seconds): self
  {
    $this->delay = now()->addSeconds($seconds);
    return $this;
  }
  
  /**
   * Send the email to the receivers
   *
   * @param mixed $emails
   * @param mixed $email_class
   * @param mixed $data
   * @return bool
  */
  public function send(mixed $emails, mixed $email_class, mixed $data): bool
  {
    try {
      $this->setReceivers($emails);
      if(!$this->receivers) {
        $this->errorLog('No receivers found ' . json_encode($emails));
      }

      if($this->delay) {
        Mail::to($this->receivers)->later($this->delay, new $email_class($data));
      } else {
        Mail::to($this->receivers)->send(new $email_class($data));
      }

      return true;
    } catch (Exception $ex) {
      $this->log_service->error($ex->getMessage());
      return false;
    }
  }
  
  /**
   * Set the receivers if single or multiple
   *
   * @param mixed $emails
   * @return void
  */
  private function setReceivers(mixed $emails)
  {
    if(is_string($emails)) {
      $this->receivers = [$emails];
    }

    if(is_array($emails)) {
      $this->receivers = $emails;
    }
  }
}
