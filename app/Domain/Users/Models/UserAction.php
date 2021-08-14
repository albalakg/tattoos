<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Users\Models\LuUserActionType;


class UserAction extends Model
{
  protected $hidden = [
    'token'
  ];

  protected $guarded = [];

  public function user()
  {
    return $this->hasOne(User::class);
  }

  public function action()
  {
    return $this->hasOne(LuUserActionType::class, 'id', 'action_type_id');
  }
}