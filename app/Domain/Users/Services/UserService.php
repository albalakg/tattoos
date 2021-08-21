<?php

namespace App\Domain\Users\Services;

use Exception;
use App\Events\Users\NewUser;
use App\Domain\Users\Models\Role;
use App\Domain\Users\Models\User;
use App\Domain\Helpers\LogService;
use App\Events\Users\NewUserEvent;
use App\Domain\Helpers\MailService;
use App\Domain\Helpers\TokenService;
use App\Domain\Helpers\StatusService;
use App\Domain\Users\Models\UserAction;
use App\Domain\Users\Models\UserDetail;
use App\Mail\Auth\EmailVerificationMail;
use App\Domain\Users\Models\LuUserActionType;
use App\Domain\Interfaces\IBaseServiceInterface;

class UserService implements IBaseServiceInterface
{
  public function __construct()
  {
    $this->setLogFile('users');
  }
  
  /**
   * Check if a user is active
   *
   * @param object $user
   * @return bool
  */
  public function isActive(object $user) :bool
  {
    return $user->status === StatusService::ACTIVE;
  }
  
  /**
   * @param object $data
   * @return object|null
  */
  public function signup(object $data)
  {
    $user = $this->createUser($data);
    event(new NewUserEvent($user));
  }
  
  
  /**
   * @param object $data
   * @return User|null 
  */
  public function createUser(object $data): ?User
  {
    $user = new User;
    $user->email = $data->email;
    $user->password = bcrypt($data->password);
    $user->status = StatusService::PENDING;
    $user->save();

    return $user;
  }
  
  /**
   * Create a record for the user's details
   *
   * @param array $data
   * @return UserDetail|null
   */
  public function createUserDetails(array $data): ?UserDetail
  {
    // TODO
    return null;
  }
}