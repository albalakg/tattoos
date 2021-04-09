<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use App\Domain\Studios\Models\Studio;
use Illuminate\Database\Eloquent\Model;

class UserFollowStudio extends Model
{
  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id')
                ->select('id', 'first_name', 'last_name');
  }

  public function studio()
  {
    return $this->hasOne(Studio::class, 'id', 'studio_id')
                ->select('id', 'name');
  }
}