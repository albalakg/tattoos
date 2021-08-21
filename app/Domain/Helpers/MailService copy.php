<?php

namespace App\Services\Mail;

use Exception;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use App\Services\Logger\LoggerService;
use Carbon\Carbon;

class MailService
{
  const MAIL_LOG_DRIVER = 'mail';

  /**
   * @var array
   */
  private $emails;

  public function __construct(array $emails = [])
  {
    $this->setEmails($emails);
  }

  /**
   * @param array $emails
   */
  public function setEmails(array $emails)
  {
    $this->emails = $emails;
  }

  /**
   * Send mail to all conifgurable emails
   *
   * @param string $mail_class
   * @param object $mail_data
   * @return boolean
   */
  public function sendBulk(string $mail_class, object $mail_data)
  {
    $this->infoLog('Attempting to send mail: ' . $mail_class);
    try {
      Mail::to($this->emails)->send($this->getMailClass($mail_class, $mail_data));
      $this->infoLog('Mail ' . $mail_class . ' sent successfully to' . json_encode($this->emails));
      return true;
    } catch (Exception $ex) {
      $this->errorLog('Failed to send mail: ' . $mail_class . ' message: ' . $ex->getMessage());
      return false;
    }
  }

  /**
   * Send mail to a single email domain
   *
   * @param string $email
   * @param string $mail_class
   * @param object $mail_data
   * @return boolean
   */
  public function sendSingle(string $email, string $mail_class, object $mail_data)
  {
    $this->infoLog('Attempting to send mail: ' . $mail_class);
    try {
      Mail::to([$email])->send($this->getMailClass($mail_class, $mail_data));
      $this->infoLog('Mail ' . $mail_class . ' sent successfully to' . $emails);
      return true;
    } catch (Exception $ex) {
      $this->errorLog('Failed to send mail: ' . $mail_class . ' message: ' . $ex->getMessage());
      return false;
    }
  }

  /**
   * Queue mail to a single email domain
   *
   * @param string $email
   * @param string $mail_class
   * @param object $mail_data
   * @param \Carbon\Carbon|null $when when to send the mail
   * @return boolean
   */
  public function queueSingle(string $email, string $mail_class, object $mail_data, Carbon $when = null, string $queue_name='default')
  {
    $this->infoLog('Attempting to send mail: ' . $mail_class);
    try {
      $when = is_null($when) ? now()->addSeconds(1) : $when;
      Mail::to([$email])->later($when, $this->getMailClassAndSetQueueName($mail_class, $mail_data,$queue_name));
      $this->infoLog('Mail ' . $mail_class . ' sent successfully to' . $email);
      return true;
    } catch (Exception $ex) {
      $this->errorLog('Failed to send mail: ' . $mail_class . ' message: ' . $ex->getMessage());
      return false;
    }
  }

  /**
   * Queue all mails to all configurable emails
   *
   * @param string $mail_class
   * @param object $mail_data
   * @param \Carbon\Carbon|null $when when to send the mail
   * @return boolean
   */
  public function queueBulk(string $mail_class, object $mail_data, Carbon $when = null,string $queue_name='default')
  {
    $this->infoLog('Attempting to send mail: ' . $mail_class);
    try {
      $when = is_null($when) ? now()->addSeconds(1) : $when;
      Mail::to($this->emails)->later($when, $this->getMailClassAndSetQueueName($mail_class, $mail_data,$queue_name));
      $this->infoLog('Mail ' . $mail_class . ' sent successfully to' . json_encode($this->emails));
      return true;
    } catch (Exception $ex) {
      $this->errorLog('Failed to send mail: ' . $mail_class . ' message: ' . $ex->getMessage());
      return false;
    }
  }



  /**
   * @param string $mail_class
   * @param object $mail_data
   * @return Mailable
   */
  private function getMailClass(string $mail_class, object $mail_data)
  {
    if(strpos($mail_class,'App\\Mail\\')===false)
    {
      $mail_class = 'App\\Mail\\' . $mail_class;
    }
    return new $mail_class($mail_data);
  }

  /**
   * @param string $mail_class
   * @param object $mail_data
   * @return Mailable
   */
  private function getMailClassAndSetQueueName(string $mail_class, object $mail_data,string $queue_name)
  {    
    if(strpos($mail_class,'App\\Mail\\') === false)
    {
      $mail_class = 'App\\Mail\\' . $mail_class;
    }
    $this->infoLog('mail data: ' . json_encode($mail_data));
    $mail = new $mail_class($mail_data);
    return $mail->onQueue($queue_name);
  }

  /**
   * @param string $message
   * @return void
   */
  private function infoLog(string $message)
  {
    LoggerService::init('INFO', $message, self::MAIL_LOG_DRIVER);
  }

  /**
   * @param string $message
   * @return void
   */
  private function errorLog(string $message)
  {
    LoggerService::init('ERROR', $message, self::MAIL_LOG_DRIVER);
  }
}
