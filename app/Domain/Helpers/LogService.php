<?php

namespace App\Domain\Helpers;

use Exception;
use App\Domain\Helpers\FileService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LogService
{  
  /**
   * Create a new error log
   *
   * @param string $content
   * @param string $logger
   * @return bool
  */
  static public function error(string $content, string $logger)
  {
    try {
      $full_log_content = self::prepareLog($content);
      self::setLog($full_log_content, 'error', $logger);
    } catch (Exception $ex) {
      Log::critical($ex->getMessage());
    }
  }

  /**
   * Create a new info log
   *
   * @param string $content
   * @param string $logger
   * @return bool
  */
  static public function info(string $content, string $logger)
  {
    try {
      $full_log_content = self::prepareLog($content);
      self::setLog($full_log_content, 'info', $logger);
    } catch (Exception $ex) {
      Log::critical($ex->getMessage());
    }
  }

  /**
   * Create a new critical log
   *
   * @param string $content
   * @param string $logger
   * @return bool
  */
  static public function critical(string $content, string $logger)
  {
    try {
      $full_log_content = self::prepareLog($content);
      self::setLog($full_log_content, 'critical', $logger);
    } catch (Exception $ex) {
      Log::critical($ex->getMessage());
    }
  }
  
  /**
   * Prepare log content
   *
   * @param string $content
   * @return string
   */
  static public function prepareLog(string $content)
  {
    try {
      $content  = "ACTION: $content, ";
      $content .= 'BROWSER: ' . request()->header('user-agent') . ', ';
      $content .= 'IP: ' . request()->ip() . ', ';
      $content .= 'USER: ' . Auth::user() ? Auth::user()->id : 'unknown' . ', ';
      $content .= 'URL: ' . request()->server('HTTP_REFERER');

      return $content;
    } catch (Exception $ex) {
      Log::critical($ex->getMessage());
      return '';
    }
  }
  
  /**
   * Create a new log
   *
   * @param string $content
   * @param string $type
   * @param string $logger
   * @return void
   */
  static public function setLog(string $content, string $type, string $logger)
  {
    try {
      Log::channel($logger)->$type($content);
    } catch (Exception $ex) {
      Log::critical($ex->getMessage());
    }
  }
  
  /**
   * Get the log
   *
   * @param string $log_name
   * @return string
   */
  static public function getLog(string $log_name)
  {
    try {
      $fileService = new FileService();
      $log_path = 'logs/' . $log_name;
      if(!$log_content = $fileService->get($log_path)) {
        throw new Exception('Failed to get the log content');
      }
      
      return $log_content;
    } catch (Exception $ex) {
      Log::critical($ex->getMessage());
      return '';
    }
  }
  
  /**
   * Search a key inside a log
   *
   * @param string $log_name
   * @param string $search_key
   * @return string
   */
  static public function searchInLog(string $log_name, string $search_key)
  {
    try {
      if(!$log_content = self::getLog($log_name)) {
        return '';
      }

      // TODO: search in a log

    } catch (Exception $ex) {
      Log::critical($ex->getMessage());
      return '';
    }
  }
}