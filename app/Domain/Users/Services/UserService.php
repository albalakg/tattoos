<?php

namespace App\Domain\Users\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Domain\Users\Models\Role;
use App\Domain\Users\Models\User;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Facades\Hash;
use App\Domain\Helpers\StatusService;
use App\Events\Users\UserCreatedEvent;
use App\Events\Users\UserDeletedEvent;
use App\Domain\Users\Models\UserDetail;
use App\Events\Users\UserResetPasswordEvent;
use App\Domain\Users\Models\UserResetPassword;
use App\Domain\Users\Models\UserEmailVerification;
use App\Services\Mail\MailService;

class UserService
{  
  /**
   * @var LogService
  */
  private $log_service;
  
  public function __construct()
  {
    $this->log_service = new LogService('users');
  }
  
  /**
   * @return object
  */
  public function getAll(): object
  {
    return User::join('roles', 'roles.id', 'users.role_id')
              ->join('user_details', 'user_details.user_id', 'users.id')
              ->select(
                'users.id',
                'users.status',
                'users.created_at',
                'users.email',
                'user_details.phone',
                'user_details.first_name',
                'user_details.last_name',
                'user_details.gender',
                'user_details.birth_date',
                'roles.name AS role'
              )
              ->orderBy('users.created_at', 'desc')
              ->simplePaginate(1000);
  }
  
  /**
   * @param string $email
   * @return User
  */
  public function getUserByEmail(string $email): User
  {
    return User::where('email', $email)->first();
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
      $data->role_id  = Role::NORMAL;
      $user           = $this->saveUser($data);
      dd($user);
      $data->user_id  = $user->id;
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
   * Create user by an admin
   *
   * @param object $data
   * @param int|null $created_by
   * @return User|null
  */
  public function createUser(object $data, ?int $created_by): ?User
  {
    try {
      $data->created_by = $created_by;
      $data->role_id    = Role::ROLES_LIST[strtolower($data->role)];
      if(!$user = $this->saveUser($data)) {
        throw new Exception('Failed to create a user');
      }

      $data->user_id    = $user->id;
      $this->saveUserDetails($data);
      return $user;
    } catch(Exception $ex) {
      if(isset($user) && $user) {
        $this->deleteUser($user->id);
      }
      throw $ex;
    }
  }
    
  /**
   * Update user by an admin
   *
   * @param object $data
   * @param int|null $created_by
   * @return User|null
  */
  public function updateUser(object $data, ?int $updated_by)
  {
    try {
      $data->updated_by = $updated_by;
      $data->role_id    = Role::ROLES_LIST[strtolower($data->role)];
      if(!$user = $this->saveUser($data)) {
        throw new Exception('Failed to update a user');
      }

      $data->user_id    = $user->id;
      $this->saveUserDetails($data);
      return $data;
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
    if(isset($data->id)) {
      if(!$user = User::find($data->id)) {
        throw new Exception('Failed to find user');
      }
    } else {
      $user = new User();
    }

    $user->role_id      = $data->role_id;
    $user->status       = StatusService::PENDING;

    if(isset($data->email)) {
      $user->email        = $data->email;
    }

    if(isset($data->password)) {
      $user->password     = bcrypt($data->password);
    }

    if(isset($data->created_by)) {
      $user->created_by   = $data->created_by;
    }

    $user->save();
    return $user;
  }
  
  /**
   * @param array $ids
   * @param int $deleted_by
   * @return void
  */
  public function deleteUsers(array $ids, int $deleted_by)
  {
    foreach($ids AS $id) {
      $this->deleteUser($id);
    }
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
      throw new Exception('Failed to reset password, request not found');
    }
    
    $user = $this->getUserByEmail($email);
    if(!$user) {
      throw new Exception('Failed to reset password, user not found');
    }

    $reset_password_request->update([
      'status' => StatusService::ACTIVE,
      'verified_at' => now()
    ]);

    $this->savePassword($user->email, $password);
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
   * @param User $user
   * @param string $old_password
   * @param string $new_password
   * @return void
  */
  public function changePassword(User $user, string $old_password, string $new_password)
  {
    if (!Hash::check($old_password, $user->password)) {
      throw new Exception('Old password is incorrect');
    }

    $this->savePassword($user->id, $new_password);
  }
  
  /**
   * @param object $data
   * @param int $updated_by
   * @return bool
  */
  public function updateUserEmail(object $data, int $updated_by): bool
  {
    return User::where('id', $data->id)->update(['email' => $data->email]);
  } 
  
  /**
   * @param object $data
   * @param int $updated_by
   * @return bool
  */
  public function updateUserPassword(object $data, int $updated_by): bool
  {
    return $this->savePassword($data->id, $data->password);
  } 
   
  /**
   * @param User $user
   * @param string $email
   * @param string $password
   * @return void
  */
  public function changeEmail(User $user, string $email, string $password)
  {
    if (!Hash::check($password, $user->password)) {
      throw new Exception('Password does is incorrect');
    }

    $user->email_verification = $this->saveEmailVerification($user->id, $email);
    $mailService = new MailService;
    // $mailService->delay(1)->send($email, UpdateEmailMail:class, $user);
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
   * @param int $user_id
   * @param string $email
   * @return bool
  */
  private function saveEmail(int $user_id, string $email): bool
  {
    return User::where('id', $user_id)->update(['email' => $email]);
  }
  
  /**
   * @param int $user_id
   * @param string $password
   * @return bool
  */
  private function savePassword(int $user_id, string $password): bool
  {
    return User::where('id', $user_id)->update(['password' => bcrypt($password)]);
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
    if(isset($data->id)) {
      if(!$user_data = UserDetail::where('user_id', $data->id)->first()) {
        throw new Exception('Failed to find user');
      }
    } else {
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