<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
  public $incrementing = false;

  public $timestamps = false;

  const GENDER_VALUES = [1, 2, 3];

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
  
  public function getFullNameAttribute()
  {
    return $this->first_name . ' ' . $this->last_name;
  }
}