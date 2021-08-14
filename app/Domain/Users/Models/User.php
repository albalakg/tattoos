<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\Role;
use Laravel\Passport\HasApiTokens;
use App\Domain\Users\Models\UserDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
  use HasFactory, HasApiTokens, SoftDeletes;

  protected $hidden = [
    'password'
  ];

  public function role()
  {
    return $this->hasOne(Role::class);
  }
  
  public function details()
  {
    return $this->hasOne(UserDetail::class, 'user_id', 'id');
  }

  public function isNormalUser()
  {
    return $this->role_id === Role::NORMAL;
  }

  public function isAdmin()
  {
    return $this->role_id === Role::ADMIN;
  }

  public function fullName()
  {
    return $this->details->first_name . ' ' . $this->details->last_name;
  }
}