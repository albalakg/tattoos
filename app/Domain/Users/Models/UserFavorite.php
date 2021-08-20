<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserFavorite extends Model
{
  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
}