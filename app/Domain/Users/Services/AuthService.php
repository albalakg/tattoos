<?php

namespace App\Domain\Users\Services;

use Exception;
use App\Domain\Users\Models\Role;
use App\Domain\Users\Models\User;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\BaseService;
use Illuminate\Support\Facades\Auth;
use App\Domain\Interfaces\IBaseServiceInterface;

class AuthService extends BaseService
{
  
  /**
   * UserService
   *
   * @var IBaseServiceInterface
  */
  private $userService;

  /**
   * @var LogService
  */
  private $log_service;
  
  public function __construct($userService)
  {
    $this->setLogFile('auth');
    $this->log_service = new LogService('auth');
    $this->userService = $userService;
  }
  
  /**
   * Logout a user
   *
   * @return bool
  */
  public function logout() :bool
  {
    Auth::user()->token()->revoke();
    $this->log_service->info('User logged out');
    return true;
  }

  # TODO: Fix the logs info

  /**
   * Logout another user
   *
   * @param App\Domain\Users\Models\User $user
   * @param int $logged_out_user_id
   * @return bool
   */
  public function logoutOtherUser(object $user, int $logged_out_user_id) :bool
  {
    $logged_out_user = User::find($logged_out_user_id);
    if(!$logged_out_user) {
      $this->log_service->info('User ' . $logged_out_user_id->id . ' not found');
      return false;
    }

    Auth::setUser($logged_out_user);
    Auth::user()->token()->revoke();
    Auth::setUser($user);

    $this->log_service->info('User ' . $logged_out_user_id->id . ' has been logged out');

    return true;
  }
}