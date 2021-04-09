<?php

namespace App\Domain\Users\Models;

use Illuminate\Database\Eloquent\Model;

class UserFriend extends Model
{
  public function user()
  {
    return $this->hasOne(User::class, 'id', 'friend_id')
                ->select('id', 'first_name', 'last_name');
  }
}