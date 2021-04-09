<?php

namespace App\Domain\Helpers;

use App\Domain\Helpers\LogService;
use Illuminate\Support\Facades\Mail;

class MailService
{  
  /**
   * Send mail
   *
   * @param  string $mail
   * @param  mixed $data
   * @param  mixed $recievers
   * 
   * @return void
   */
  static public function send(string $mail, mixed $data, mixed $recievers)
  {
    try{
      self::setLog($mail, $recievers);

      // TODO: enter to queue
      Mail::to($recievers)->send($mail, $data);
    } catch(Exception $ex) {
      LogService::error($ex->getMessage(), 'mail');
    }
  }
  
  /**
   * Create a log for attempting to send an email
   *
   * @param  string $mail
   * @param  mixed $recievers
   * @return void
   */
  static private function setLog(string $mail, mixed $recievers)
  {
    $recievers = is_string($recievers) ? $recievers : json_encode($recievers);
    LogService::info("Attempt to send mail: $mail to: $recievers", 'mail');
  }
}