<?php

namespace App\Domain\Helpers;

use App\Domain\Helpers\LogService;
use App\Domain\Helpers\ResponseService;

class BaseService extends ResponseService
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