<?php

namespace App\Domain\Helpers;

use Illuminate\Support\Str;

class TokenService
{
  const BASE_CHARS = 40;

  static public function getToken(int $chars = self::BASE_CHARS) :string
  {
    return Str::random($chars);
  }
}