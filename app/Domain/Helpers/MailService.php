<?php

namespace App\Domain\Helpers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Mail\Mailable;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Facades\Mail;
use App\Domain\Emails\Services\EmailService;

class MailService
{
  const MAIL_LOG_DRIVER = 'mail';
  const DEFAULT_DELAY = 1; // Seconds

  const SYSTEM_EMAILS = [
    'gal.blacky@gmail.com'
  ];

  /**
   * @var array
   */
  private array $receivers;

  /**
   * @var int
   */
  private int $delay_time = 0;

  /**
   * @var string
   */
  private string $mail_track_id;

  /**
   * @var Boolean
   */
  private bool $isMock = false;

  /**
   * @var LogService
   */
  private LogService $log_service;

  /**
   * @var EmailService
   */
  private EmailService $email_service;

  public function __construct()
  {
    $this->email_service  = new EmailService;
    $this->log_service    = new LogService('mail');
    $this->mail_track_id  = Str::uuid();
    $this->info('Mail service triggered');
  }
  
  /**
   * Send the mail in queue, the default is 1 second
   *
   * @param int $seconds
   * @return self
  */
  public function delay(int $seconds = self::DEFAULT_DELAY): self
  {
    $this->delay_time = $seconds;
    $this->info('Delay triggered with: ' . $seconds . ' seconds');
    return $this;
  }
  
  /**
   * Change the mail server non functional
   * for tests and mocks
   *
   * @return self
  */
  public function mock(): self
  {
    $this->isMock = true;
    $this->info('Mock triggered');
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
      $this->info('Sending mail', ['email' => $email_class]);

      // If mail service is off skip
      if(!config('mail.status') || $this->isMock) {
        $this->info('Mail service is not active');
        return true;
      }

      $this->setReceivers($emails);
      if(!$this->receivers) {
        throw new Exception('No receivers found');
      }

      $email_sent = $this->email_service->create(
        $this->receivers,
        $email_class,
        $data
      );

      if($this->delay_time) {
        Mail::to($this->receivers)->later($this->delay_time, new $email_class($data));
      } else {
        Mail::to($this->receivers)->send(new $email_class($data));
      }

      $this->info('Mail sent successfully');
      $this->email_service->updateStatus($email_sent->id, StatusService::ACTIVE);
      
      return true;
    } catch (Exception $ex) {
        $this->email_service->updateStatus($email_sent->id, StatusService::INACTIVE);
      $this->error($ex->__toString());
      return false;
    }
  }
  
  /**
   * Set the receivers if single or multiple
   *
   * @param mixed $emails
   * @return void
  */
  private function setReceivers($emails)
  {
    if(is_string($emails)) {
      $this->receivers = [$emails];
    }

    if(is_array($emails)) {
      $this->receivers = $emails;
    }

    $this->info('Receivers are: ' . json_encode($this->receivers));
  }
  
  /**
   * @param string $content
   * @return void
  */
  private function info(string $content)
  {
    $this->log_service->info($content, [LogService::TRACK_ID => $this->mail_track_id]);
  }
  
  /**
   * @param string $content
   * @return void
  */
  private function error(string $content)
  {
    $this->log_service->error($content, [LogService::TRACK_ID => $this->mail_track_id]);
  }
}
