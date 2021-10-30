<?php

namespace App\Domain\Helpers;

use Exception;
use Illuminate\Mail\Mailable;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Facades\Mail;
use App\Domain\Emails\Services\EmailService;

class MailService
{
  const MAIL_LOG_DRIVER = 'mail';
  const DEFAULT_DELAY = 1; // Seconds

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

  /**
   * @var EmailService
   */
  private $emailService;

  public function __construct()
  {
    $this->emailService = new EmailService;
    $this->log_service = new LogService('mail');
  }
  
  /**
   * Send the mail in queue
   *
   * @param int $seconds
   * @return self
  */
  public function delay(int $seconds = self::DEFAULT_DELAY): self
  {
    $this->delay = now()->addSeconds($seconds);
    return $this;
  }
  
  /**
   * Send the email to the receivers
   *
   * @param string|array $emails
   * @param string $email_class
   * @param object|array $data
   * @return bool
  */
  public function send($emails, string $email_class, $data): bool
  {
    try {
      // If mail service is off skip
      if(!config('mail.status')) {
        return true;
      }

      $this->setReceivers($emails);
      if(!$this->receivers) {
        throw new Exception('No receivers found');
      }

      $email_sent = $this->emailService->create(
        $this->receivers,
        $email_class,
        $data
      );

      if($this->delay) {
        Mail::to($this->receivers)->later($this->delay, new $email_class($data));
      } else {
        Mail::to($this->receivers)->send(new $email_class($data));
      }

      $this->emailService->updateStatus($email_sent->id, StatusService::ACTIVE);
      
      return true;
    } catch (Exception $ex) {
      $this->emailService->updateStatus($email_sent->id, StatusService::INACTIVE);
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
