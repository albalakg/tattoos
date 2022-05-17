<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use App\Domain\Users\Models\LuCity;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
  public $incrementing = false;

  public $timestamps = false;

  const GENDER_VALUES = [1, 2, 3];

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
  
  public function city()
  {
    return $this->hasOne(LuCity::class, 'id', 'city_id')
                ->select('city_id', 'name');
  }
  
  public function team()
  {
    return $this->hasOne(LuTeam::class, 'id', 'team_id')
                ->select('city_id', 'name');
  }
  
  protected $appends = ['fullName'];

  public function getFullNameAttribute()
  {
    return $this->first_name . ' ' . $this->last_name;
  }
}