<?php

namespace App\Domain\Helpers;

use Exception;
use App\Jobs\Helpers\MailPodcast;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Facades\Mail;

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