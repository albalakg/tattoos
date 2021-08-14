<?php

namespace App\Domain\Helpers;

class BaseService
{
  public $log_file = 'custom';
 
  /**
   * Set log file
   *
   * @param string $log_file
   */
  protected function setLogFile(string $log_file)
  {
    $this->log_file = $log_file;
  }
}