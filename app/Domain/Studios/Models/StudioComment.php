<?php

namespace App\Domain\Studios\Models;

use App\Domain\Users\Models\User;
use App\Domain\Studios\Models\Studio;
use Illuminate\Database\Eloquent\Model;

class StudioComment extends Model
{  
  public function studio()
  {
    return $this->hasOne(Studio::class)
                ->select('id', 'title', 'image');
  }

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id')
                ->select('id', 'first_name', 'last_name');
  }
}