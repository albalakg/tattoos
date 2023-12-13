<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserChallenge extends Model
{
  protected $guarded = [];
  
  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
  
  public function attempts()
  {
    return $this->hasMany(UserChallengeAttempt::class, 'user_challenge_id', 'id');
  }
  
  public function lastAttempt()
  {
    return $this->hasOne(UserChallengeAttempt::class, 'user_challenge_id', 'id')
                ->orderBy('id', 'desc')
                ->select('id', 'user_challenge_id', 'video', 'status', 'created_at');
  }
}