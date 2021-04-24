<?php

namespace App\Domain\Helpers;

use Exception;
use App\Jobs\Helpers\MailPodcast;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Facades\Mail;
use App\Mail\Application\ApplicationErrorMail;

class MailService
{  
  /**
   * Send mail
   * @param \Illuminate\Contracts\Mail\Mailable $mail
   * @param object $data
   * @param array|string $receivers
   * 
   * @return bool
  */
  static public function send(string $mail, object $data, $receivers) :bool
  {
    try{
      if(!env('MAIL_STATUS', false)) {
        LogService::info('Mail service is disabled', 'mail');
        return true;
      }

      self::setLog($mail, $receivers);
      
      MailPodcast::dispatch($mail, $data, $receivers);

      return true;
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), 'mail');
      return false;
    }
  }
  
  /**
   * Send a critical email to all the relavent people
   * The content of the email will contain the details of the error
   * @param string $content
   * 
   * @return bool
  */
  static public function criticalError(string $content) :bool
  {
    try{
      // TODO: get the receivers from database
      $data_to_send = (object) [
        'content' => $content
      ];

      self::send(ApplicationErrorMail::class, $data_to_send, 'gal.blacky@gmail.com');

      return true;
    } catch(Exception $ex) {
      return false;
    }   
  }
  
  /**
   * Create a log for attempting to send an email
   *
   * @param string $mail
   * @param array|string $receivers
   * @return void
   */
  static private function setLog(string $mail, $receivers)
  {
    $receivers = is_string($receivers) ? $receivers : json_encode($receivers);
    LogService::info("Attempt to send mail: $mail to: $receivers", 'mail');
  }
}