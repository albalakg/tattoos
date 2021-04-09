<?php

namespace App\Domain\Tattoos\Models;

use App\Domain\Users\Models\User;
use App\Domain\Tattoos\Models\Tattoo;
use Illuminate\Database\Eloquent\Model;

class TattooLike extends Model
{
  public function user()
  {
    return $this->hasOne(User::class)
                ->select('id', 'first_name', 'last_name');
  }

  public function tattoo()
  {
    return $this->hasOne(Tattoo::class)
                ->select('id', 'title', 'image');
  }
}