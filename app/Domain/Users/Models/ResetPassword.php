<?php

namespace App\Domain\Users\Models;

use Illuminate\Support\Str;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class ResetPassword extends Model
{
  protected $guarded = [];
  
  public $incrementing = false;

  /**
   * Time till the token is valid to reset the password
   * Time in minutes
   * 
   * @var int RESET_TIME 
  */
  const RESET_TIME = 60;

  public function user()
  {
    return $this->hasOne(User::class, 'email', 'email')
                ->select('id', 'email', 'first_name', 'last_name');
  }
}