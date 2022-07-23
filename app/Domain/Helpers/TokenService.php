<?php

namespace App\Domain\Helpers;

use Illuminate\Support\Str;

class TokenService
{  
  /**
   * The default amount of characters which will be the token
   *
   * @var int
  */
  const BASE_CHARS = 50;
  
  /**
   * Create a random string which is used as a token
   *
   * @param int $chars
   * @return string
  */
  static public function createToken(int $chars = self::BASE_CHARS) :string
  {
    $unique_token = encrypt(Str::random($chars));
    return substr($unique_token, 0, $chars);
  }
}