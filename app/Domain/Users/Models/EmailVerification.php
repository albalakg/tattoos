<?php

namespace App\Domain\Users\Models;

use Illuminate\Support\Str;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
  protected $guarded = [];

  protected $primaryKey = 'token';
  
  public $incrementing = false;

  public $timestamps = false;

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id')
                ->select('id', 'email', 'first_name', 'last_name');
  }
}