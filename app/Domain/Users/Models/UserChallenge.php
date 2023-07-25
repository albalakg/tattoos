<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserChallenge extends Model
{
  use SoftDeletes;
  
  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
}