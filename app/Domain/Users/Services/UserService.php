<?php

namespace App\Domain\Users\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Domain\Users\Models\Role;
use App\Domain\Users\Models\User;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\StatusService;
use App\Events\Users\UserCreatedEvent;
use App\Events\Users\UserDeletedEvent;
use App\Domain\Users\Models\UserDetail;
use App\Events\Users\UserResetPasswordEvent;
use App\Domain\Users\Models\UserResetPassword;
use App\Domain\Interfaces\IBaseServiceInterface;
use App\Domain\Users\Models\UserEmailVerification;

class UserService implements IBaseServiceInterface
{  
  /**
   * @var LogService
  */
  private $log_service;
  
  public function __construct()
  {
    $this->log_service = new LogService('user');
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
   * @param User $user
   * @return void
  */
  public function logout(User $user)
  {
    $user->token()->revoke();
  }
  
  /**
   * @param object $data
   * @return User|null
  */
  public function signup(object $data): ?User
  {
    try {
      $user = $this->saveUser($data);
      $data->user_id = $user->id;
      $this->saveUserDetails($data);
      $user->email_verification = $this->saveEmailVerification($user->id, $user->email);
      event(new UserCreatedEvent($user));
      return $user;
    } catch(Exception $ex) {
      if(isset($user) && $user) {
        $this->deleteUser($user->id);
      }
      throw $ex;
    }
  }
  
  
  /**
   * @param object $data
   * @return User|null 
  */
  public function saveUser(object $data): ?User
  {
    $user               = new User;
    $user->role_id      = Role::NORMAL;
    $user->email        = $data->email;
    $user->password     = bcrypt($data->password);
    $user->status       = StatusService::PENDING;
    if(isset($data->created_by)) {
      $user->created_by = $data->created_by;
    }

    $user->save();
    return $user;
  }
  
  /**
   * @param int $user_id
   * @return void
  */
  public function deleteUser(int $user_id)
  {
    $user = User::find($user_id);
    $user->update([
      'email' => null,
      'password' => null,
    ]);
    
    $user->delete();
    UserDetail::where('user_id', $user_id)->delete();
    event(new UserDeletedEvent($user));
  }
  
  /**
   * Create a record for the user's details
   *
   * @param object $data
   * @return UserDetail|null
   */
  public function updateUserDetails(object $data): ?UserDetail
  {
    return $this->saveUserDetails($data);
  }
    
  /**
   * @param string $email
   * @param string $token
   * @param string $password
   * @return void
  */
  public function resetPassword(string $email, string $token, string $password) 
  {
    $reset_password_request = UserResetPassword::where('email', $email)
                                     ->where('token', $token)
                                     ->where('status', StatusService::PENDING)
                                     ->first();
  
    if(!$reset_password_request) {
      throw new Exception('Failed to reset password');
    }

    $reset_password_request->update([
      'status' => StatusService::ACTIVE,
      'verified_at' => now()
    ]);
  }
    
  /**
   * @param string $email
   * @return void
  */
  public function forgotPassword(string $email) 
  {
    if(!$this->canResetPassword($email)) {
      throw new Exception('Sorry, you have reached maximum reset attempts for today');
    }

    UserResetPassword::where('email', $email)
                     ->where('status', StatusService::PENDING)
                     ->update(['status' => StatusService::INACTIVE]);


    $forgot_password_request = UserResetPassword::create([
      'token' => Str::random(50),
      'email' => $email,
      'status' => StatusService::PENDING,
      'created_at' => now()
    ]);

    event(new UserResetPasswordEvent($forgot_password_request));
  }
    
  /**
   * @param string $email
   * @param string $token
   * @return bool
  */
  public function verifyEmail(string $email, string $token): bool
  {
    $verification = UserEmailVerification::where('email', $email)
                                        ->where('token', $token)
                                        ->first();
    if(!$verification) {
      throw new Exception('Failed to verify email');
    }

    if(!$verification->verified_at) {
      $verification->update(['verified_at' => now()]);
    }

    $this->saveStatus($verification->user_id, StatusService::ACTIVE);

    return true;
  }
  
  /**
   * @param int $user_id
   * @param string $email
   * @return UserEmailVerification
  */
  private function saveEmailVerification(int $user_id, string $email): UserEmailVerification
  {
    return UserEmailVerification::create([
      'user_id' => $user_id,
      'email' => $email,
      'token' => Str::random(50),
      'created_at' => now()
    ]);
  }
  
  /**
   * @param int $user_id
   * @param int $status
   * @return bool
  */
  private function saveStatus(int $user_id, int $status): bool
  {
    return User::where('id', $user_id)->update(['status' => $status]);
  }
  
  /**
   * Check if user has requested to reset his password less then 3 times
   * in the last 24 hours 
   * 
   * @param string $email
   * @return bool
  */
  private function canResetPassword(string $email): bool
  {
    return UserResetPassword::where('email', $email)
                            ->where('created_at', '>', Carbon::now()->subMinutes(1440))
                            ->count() < 3;
  }
  
  /**
   * Save the user details
   *
   * @param object $data
   * @return UserDetail|null
  */
  private function saveUserDetails(object $data): ?UserDetail
  {
    if(!$user_data = UserDetail::find($data->user_id)) {
      $user_data = new UserDetail();
    }

    $user_data->user_id     = $data->user_id;
    $user_data->first_name  = $data->first_name;
    $user_data->last_name   = $data->last_name;

    if(isset($data->phone)) {
      $user_data->phone  = $data->phone;
    }

    if(isset($data->gender)) {
      $user_data->gender  = $data->gender;
    }

    if(isset($data->birth_date)) {
      $user_data->birth_date  = $data->birth_date;
    }

    $user_data->save();
    return $user_data;
  }
}