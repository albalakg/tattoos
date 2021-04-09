<?php

namespace App\Domain\Users\Models;

use Illuminate\Support\Str;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class ResetEmail extends Model
{
  public function user()
  {
    return $this->hasOne(User::class, 'email', 'email')
                ->select('id', 'email', 'first_name', 'last_name');
  }
}