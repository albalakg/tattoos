<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
  protected $primaryKey = 'user_id';

  public $incrementing = false;

  public $timestamps = false;

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
}