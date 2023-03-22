<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFavorite extends Model
{
  use SoftDeletes;
  
  public $timestamps = false;

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
}