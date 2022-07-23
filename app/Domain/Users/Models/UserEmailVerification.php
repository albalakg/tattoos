<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserEmailVerification extends Model
{
  protected $guarded = [];

  public $timestamps = false;

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
}