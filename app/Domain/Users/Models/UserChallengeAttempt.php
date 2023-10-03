<?php

namespace App\Domain\Users\Models;

use Illuminate\Database\Eloquent\Model;

class UserChallengeAttempt extends Model
{
  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
  ];
  
  public function userChallenge()
  {
    return $this->hasOne(UserChallenge::class, 'id', 'user_challenge_id');
  }
}