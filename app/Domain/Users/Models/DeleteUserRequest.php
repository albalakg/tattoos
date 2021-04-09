<?php

namespace App\Domain\Users\Models;

use Illuminate\Support\Str;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class DeleteUserRequest extends Model
{
  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id')
                ->select('id', 'first_name', 'last_name');
  }
}