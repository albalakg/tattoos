<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class LuUserActionType extends Model
{
  protected $guarded = [];
  
  public $timestamps = false;

  const RESET_PASSWORD = 'reset password',
        DELETE_ACCOUNT = 'delete account',
        VERIFY_EMAIL   = 'verify email';
}