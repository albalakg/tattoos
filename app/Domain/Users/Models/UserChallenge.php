<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserChallenge extends Model
{
  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
  
  public function attempts()
  {
    return $this->hasOne(UserChallengeAttempt::class, 'id', 'user_challenge_id');
  }
}