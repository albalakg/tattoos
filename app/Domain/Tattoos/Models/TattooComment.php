<?php

namespace App\Domain\Tattoos\Models;

use App\Domain\Users\Models\User;
use App\Domain\Tattoos\Models\Tattoo;
use Illuminate\Database\Eloquent\Model;

class TattooComment extends Model
{
  public function tattoo()
  {
    return $this->hasOne(Tattoo::class)
                ->select('id', 'title', 'image');
  }

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id')
                ->select('id', 'first_name', 'last_name');
  }
}