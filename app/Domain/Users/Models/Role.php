<?php

namespace App\Domain\Users\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  const VIEWER = 10,
        ARTIST = 20,
        OWNER  = 30,
        WORKER  = 40,
        ADMIN  = 50;

  const ROLES = [
    'viewer' => self::VIEWER,
    'artist' => self::ARTIST,
    'owner' => self::OWNER,
    'worker' => self::WORKER,
    'admin' => self::ADMIN,
  ];

  const NAMES_LIST = [
    'viewer',
    'artist',
    'owner',
    'worker',
    'admin',
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