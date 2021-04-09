<?php

namespace App\Domain\Helpers;

class StatusService
{
  const INACTIVE = 0,
        ACTIVE   = 1,
        PENDING  = 2;
        
  /**
   * Get all statuses
   *
   * @return array
   */
  static public function getAll() :array
  {
    return [
      self::INACTIVE,
      self::ACTIVE,
      self::PENDING
    ];
  }
}