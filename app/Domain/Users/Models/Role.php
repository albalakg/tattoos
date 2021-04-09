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

  const ROLES_LIST = [
    'viewer' => self::VIEWER,
    'artist' => self::ARTIST,
    'owner' => self::OWNER,
    'worker' => self::WORKER,
    'admin' => self::ADMIN,
  ];

  const IDS_LIST = [
    self::VIEWER => 'viewer',
    self::ARTIST => 'artist',
    self::OWNER => 'owner',
    self::WORKER => 'worker',
    self::ADMIN => 'admin',
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
    return self::ROLES_LIST[$role_name];
  }     

  /**
   * Get the role name by the id
   *
   * @param int $role_id
   * @return string
  */
  static public function getRoleName(int $role_id) :string
  {
    return self::IDS_LIST[$role_id];
  }     
}