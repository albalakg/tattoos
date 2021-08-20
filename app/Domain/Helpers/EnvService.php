<?php

namespace App\Domain\Helpers;


class EnvService
{
  const LOCAL = 'local',
        PROD  = 'prod';


  static public function isProd()
  {
    return config('app.env') === self::PROD;
  }

  static public function iNotsProd()
  {
    return config('app.env') !== self::PROD;
  }

  static public function isLocal()
  {
    return config('app.env') === self::LOCAL;
  }
}