<?php

namespace App\Domain\Users\Models;

use App\Domain\Helpers\StatusService;
use App\Domain\Users\Models\Role;
use Laravel\Passport\HasApiTokens;
use App\Domain\Users\Models\UserDetail;
use App\Domain\Users\Models\UserFavorite;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
  
  public function favorites()
  {
    return $this->hasMany(UserFavorite::class, 'user_id', 'id');
  }
  
  public function courses()
  {
    return $this->hasMany(UserCourse::class, 'user_id', 'id');
  }
  
  public function activeCourses()
  {
    return $this->hasMany(UserCourse::class, 'user_id', 'id')
                ->where('status', StatusService::ACTIVE);
  }
  
  public function inactiveCourses()
  {
    return $this->hasMany(UserCourse::class, 'user_id', 'id')
                ->where('status', StatusService::INACTIVE);
  }
  
  public function finishedCourses()
  {
    return $this->hasMany(UserCourse::class, 'user_id', 'id')
                ->where('progress', UserCourse::DONE);
  }
  
  public function logAttempts()
  {
    return $this->hasOne(UserLogAttempt::class, 'email', 'email');
  }

  public function isNormalUser()
  {
    return $this->role_id === Role::NORMAL;
  }

  public function isAdmin()
  {
    return $this->role_id === Role::ADMIN;
  }

  public function isActive()
  {
    return $this->status === StatusService::ACTIVE;
  }

  public function isInactive()
  {
    return $this->status === StatusService::INACTIVE;
  }

  public function isWaitingForConfirmation()
  {
    return $this->status === StatusService::PENDING;
  }
}