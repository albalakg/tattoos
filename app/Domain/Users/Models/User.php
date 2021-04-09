<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\Role;
use App\Domain\Users\Models\UserDetail;
use App\Domain\Users\Models\UserFriend;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Users\Models\UserSavedTattoo;
use App\Domain\Users\Models\UserFollowStudio;
use App\Domain\Users\Models\UserWatchedTattoo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
  use HasFactory;

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

  public function likedTattoos()
  {
    return $this->hasMany(UserLikedTattoo::class, 'user_id', 'id');
  }

  public function watchedTattoos()
  {
    return $this->hasMany(UserWatchedTattoo::class, 'user_id', 'id');
  }

  public function savedTattoos()
  {
    return $this->hasMany(UserSavedTattoo::class, 'user_id', 'id');
  }

  public function friends()
  {
    return $this->hasMany(UserFriend::class, 'user_id', 'id');
  }

  public function followingStudios()
  {
    return $this->hasMany(UserFollowStudio::class, 'user_id', 'id');
  }

  public function isAdmin()
  {
    return $this->role->id === Role::ADMIN;
  }
  
  public function isOwner()
  {
    return $this->role->id === Role::OWNER;
  }

  public function isArtist()
  {
    return $this->role->id === Role::ARTIST;
  }

  public function isViewer()
  {
    return $this->role->id === Role::VIEWER;
  }
}