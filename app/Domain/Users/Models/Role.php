<?php

namespace App\Domain\Users\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  const VIEWER = 10,
        ARTIST = 20,
        OWNER  = 30,
        ADMIN  = 40;

  const ROLES = [
    'viewer' => self::VIEWER,
    'artist' => self::ARTIST,
    'owner' => self::OWNER,
    'admin' => self::ADMIN,
  ];

  const NAMES_LIST = [
    'viewer',
    'artist',
    'owner',
    'admin',
  ];

  const IDS_LIST = [
    10,
    20,
    30,
    40,
  ];
  
  /**
   * Get the role id by the name
   *
   * @param string $role_name
   * @return int
  */
  static public function getRoleId(string $role_name) :int
  {
    return self::ROLES[$role_name];
  }     
}