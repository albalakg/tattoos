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

  public function __construct($userService)
  {
    $this->setLogFile('auth');
    $this->userService = $userService;
  }
  
  /**
   * Logout a user
   *
   * @return bool
  */
  public function logout() :bool
  {
    try {
      Auth::user()->token()->revoke();
      return true;
    } catch(Exception $ex) {
      LogService::error( __METHOD__ . $ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Logout another user
   *
   * @param App\Domain\Users\Models\User $user
   * @param int $logged_out_user_id
   * @return bool
   */
  public function logoutOtherUser(object $user, int $logged_out_user_id) :bool
  {
    try {
      $logged_out_user = User::find($logged_out_user_id);

      Auth::setUser($logged_out_user);
      Auth::user()->token()->revoke();
      Auth::setUser($user);

      return true;
    } catch(Exception $ex) {
      LogService::error('logout: '. $ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Login a user
   *
   * @param string $email
   * @param string $password
   * @return array|string
  */
  public function login(string $email, string $password)
  {
    try {
      $attempt = Auth::attempt(['email' => $email, 'password' => $password]);
      if(!$attempt) {
        throw new Exception('Email or password is incorrect');
      }

      if(!$this->userService->isActive(Auth::user())) {
        throw new Exception('User is unauthorized');
      }

      $token = $this->createLoginMetaData(Auth::user());
      if(!$token) {
        throw new Exception('Failed to create a user token');
      }

      $user_id = Auth::user()->id;

      LogService::info("User $user_id logged in successfully", $this->log_file);
      return $token;
    } catch(Exception $ex) {
      LogService::error(__METHOD__ . $ex->getMessage(), $this->log_file);
      return $ex->getMessage();
    }
  }
  
  /**
   * Create an object of meta data about the user
   *
   * @param object $user
   * @return object|null
  */
  private function createLoginMetaData(object $user) : ?object
  {
    try {
      return (object) [
        'token' => $this->createToken($user),
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => $user->email,
        'role' => Role::getRoleName($user->role_id),
      ];
    } catch(Exception $ex) {
      LogService::error('createUserToken: ' . $ex->getMessage(), $this->log_file);
      return null;
    }
  }
  
  /**
   * Create token
   *
   * @param object $user
   * @return string
  */
  private function createToken(object $user)
  {
    return $user->createToken('Goldens')->accessToken;
  }
}