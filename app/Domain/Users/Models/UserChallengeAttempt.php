<?php

namespace App\Domain\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserChallengeAttempt extends Model
{
  use SoftDeletes;
  
  public function userChallenge()
  {
    return $this->hasOne(UserChallenge::class, 'id', 'user_challenge_id');
  }
}