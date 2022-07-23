<?php

namespace App\Domain\Helpers;

use Exception;

class DataManipulationService
{  

  /**
   * Receives a int or array but always return an array
   *
   * @param int|array $data
   * @return array
  */
  static public function intToArray($data): array
  {
    if(is_numeric($data)) {
      return [$data];
    } 
    
    if(is_array($data)) {
      return $data;
    }
    
    throw new Exception('data must be int or array');
  }
}