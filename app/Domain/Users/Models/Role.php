<?php

namespace App\Domain\Users\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  const NORMAL = 10,
        ADMIN  = 20;

  const IDS = [self::NORMAL, self::ADMIN];
  const NAMES = ['normal', 'admin'];

  const ROLES_LIST = [
    'normal' => self::NORMAL,
    'admin' => self::ADMIN,
  ];

  const IDS_LIST = [
    self::NORMAL => 'normal',
    self::ADMIN => 'admin',
  ];

  /**
   * Get the role id by the name
   *
   * @param string $role_name
   * @return int
  */
  static public function getRoleId(string $role_name) :int
  {
    return self::ROLES_LIST[strtolower($role_name)];
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