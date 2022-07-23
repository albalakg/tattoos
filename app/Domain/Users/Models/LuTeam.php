<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\UserDetail;
use Illuminate\Database\Eloquent\Model;

class LuTeam extends Model
{
  protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
  ];

  protected $guarded = [];

  public $timestamps = false;
    
  public function users()
  {
    return $this->hasMany(UserDetail::class, 'id', 'city_id');
  }
}