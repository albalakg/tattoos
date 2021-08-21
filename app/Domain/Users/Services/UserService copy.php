<?php

namespace App\Domain\Users\Services;

use Exception;
use Illuminate\Support\Carbon;
use App\Domain\Users\Models\Role;
use App\Domain\Users\Models\User;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\BaseService;
use App\Domain\Helpers\MailService;
use App\Domain\Helpers\TokenService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Domain\Helpers\StatusService;
use App\Mail\Auth\ForgotPasswordMail;
use App\Domain\Users\Models\ResetEmail;
use App\Domain\Users\Models\UserDetail;
use App\Domain\Users\Models\UserFriend;
use App\Mail\Auth\EmailVerificationMail;
use App\Mail\User\DeleteAccountMail;
use App\Domain\Helpers\PaginationService;
use App\Domain\Tattoos\Models\TattooLike;
use App\Domain\Tattoos\Models\TattooSave;
use App\Mail\User\UpdateEmailRequestMail;
use App\Domain\Studios\Models\StudioWatch;
use App\Domain\Tattoos\Models\TattooWatch;
use App\Domain\Users\Models\ResetPassword;
use App\Domain\Users\Models\UserFollowStudio;
use App\Domain\Studios\Services\StudioService;
use App\Domain\Tattoos\Services\TattooService;
use App\Domain\Users\Models\DeleteAccount;
use App\Domain\Users\Models\EmailVerification;

class UserService extends BaseService
{
  public function __construct()
  {
    $this->setLogFile('users');
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
      LogService::error('logout: '. $ex->getMessage(), $this->log_file);
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

      if(!$this->userIsActive(Auth::user())) {
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
      LogService::error('login: '. $ex->getMessage(), $this->log_file);
      return $ex->getMessage();
    }
  }
  
  /**
   * Create an object of meta data about the user
   *
   * @param object $user
   * @return array|null
  */
  private function createLoginMetaData(object $user)
  {
    try {
      return [
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
    return $user->createToken('MiToo')->accessToken;
  }
  
  /**
   * Check if a user is active
   *
   * @param object $user
   * @return bool
  */
  private function userIsActive(object $user)
  {
    return $user->status === StatusService::ACTIVE;
  }

  /**
   * Signup a user
   *
   * @param array $user
   * @return object|null
  */
  public function ignup(array $user)
  {
    try {
      $new_user = new User;
      $new_user->first_name = $user['first_name'];
      $new_user->last_name = $user['last_name'];
      $new_user->email = $user['email'];
      $new_user->role_id = Role::VIEWER;
      $new_user->password = bcrypt($user['password']);
      if(!empty($user['phone'])) {
        $new_user->phone =  $user['phone'];
      }
      $new_user->save();

      $this->createUserDetails($new_user->id);
      $this->sendEmailVerification($new_user->email, $new_user->id);

      LogService::info("User $new_user->id signup successfully", $this->log_file);
      return $new_user;
    } catch(Exception $ex) {
      LogService::error('signup: ' . $ex->getMessage(), $this->log_file);
      return null;
    }
  }
  
  /**
   * Create a record for the user's details
   *
   * @param int $user_id
   * @return bool
   */
  public function createUserDetails(int $user_id) :bool
  {
    try {
      $user_details = new UserDetail;
      $user_details->user_id = $user_id;
      $user_details->save();

      return true;
    } catch(Exception $ex) {
      LogService::error('createUserDetails: ' . $ex->getMessage(), $this->log_file);
      return false;
    }   
  }
  
  /**
   * Create an email verification record and sends a verification mail
   *
   * @param string $email
   * @param int $user_id
   * @return bool
   */
  public function sendEmailVerification(string $email, int $user_id) :bool
  {
    try {
      $email_verification = EmailVerification::create([
        'user_id' => $user_id,
        'token' => TokenService::createToken(),
        'status' => StatusService::PENDING,
        'created_at' => now()
      ]);

      $data_to_send = (object) [
        'token' => $email_verification->token
      ];

      MailService::send(EmailVerificationMail::class, $data_to_send, $email);

      return true;
    } catch(Exception $ex) {
      LogService::error('createUserDetails: ' . $ex->getMessage(), $this->log_file);
      return false;
    }   
  }
  
  /**
   * Verify the email after registration
   *
   * @param string $token
   * @return bool
   */
  public function verifyEmail(string $token) :bool
  {
    try {
      if(!$email_verification = EmailVerification::where('token', $token)->first()) {
        throw new Exception('Unabled to verify email, no record was found');
      }

      $email_verified_already = UserDetail::where('user_id', $email_verification->user_id)
                                          ->whereNotNull('email_verified_at')
                                          ->exists();

      if($email_verified_already) {
        throw new Exception('Email already verified');
      }                                  

      $user_details = (object) [
        'email_verified_at' => now()
      ];
      $this->updateUserDetailValue($email_verification->user_id, $user_details);

      $email_verification->status = StatusService::ACTIVE;
      $email_verification->save();

      return true;
    } catch(Exception $ex) {
      LogService::error('verifyEmail: ' . $ex->getMessage(), $this->log_file);

      if($email_verification) {
        $email_verification->status = StatusService::INACTIVE;
        $email_verification->save();
      }

      return false;
    }   
  }
  
  /**
   * Updates a piece of data of users details
   *
   * @param int $user_id
   * @param object $column
   * @return bool
   */
  public function updateUserDetailValue(int $user_id, object $data) :bool
  {
    try {
      $user_details = UserDetail::find($user_id);

      foreach($data AS $key => $value) {
        $user_details->$key = $value;
      }

      $user_details->save();

      return true;
    } catch(Exception $ex) {
      LogService::error('updateUserDetailValue: ' . $ex->getMessage(), $this->log_file);
      return false;
    }  
  }

  /**
   * Update a user's email reqeuest
   * This requires an email confirmation
   *
   * @param object $user
   * @param string $new_email
   * @param string $password
   * @return bool
  */
  public function updateEmailRequest(object $user, string $new_email, string $password)
  {
    try {
      if (!Hash::check($user->password, $password)) {
        throw new Exception('Password is incorrect');
      }



      $reset_email = ResetEmail::create([
        'user_id' => $user->id,
        'new_email' => $new_email,
        'token' => TokenService::createToken(),
        'status' => StatusService::PENDING,
      ]);

      $data_to_send = (object) [
        'user_name' => $user->name,
        'token' => $reset_email->token
      ];

      MailService::send(UpdateEmailRequestMail::class, $data_to_send, $new_email);

      LogService::info("User $user->id request to update email", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error('updateEmailRequest: '. $ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Check if the user has already an open update email request
   *
   * @param  mixed $user_id
   * @return bool
   */
  private function userHasActiveEmailUpdateRequest(int $user_id) :bool
  {
    return ResetEmail::where('user_id', $user_id)
              ->where('status', StatusService::ACTIVE)
              ->exists();
  }
  
  /**
   * Update a user's email confirmation
   *
   * @param int $user_id
   * @param string $new_email
   * @param string $token
   * @return object|null
  */
  public function updateEmailConfirmed(int $user_id, string $new_email, string $token)
  {
    try {
      if(!$user = $this->getUserByField('id', $user_id)) {
        throw new Exception('User not found');
      }

      $reset_email_is_valid = ResetEmail::where('token', $token)
                                        ->where('new_email', $new_email)
                                        ->first();

      if(!$reset_email_is_valid) {
        throw new Exception('Email confirmation is invalid');
      }

      ResetEmail::where('id', $reset_email_is_valid->id)
                ->update([
                  'status' => StatusService::ACTIVE
                ]);

      $this->updateEmail($user->id, $new_email);
      
      LogService::info("User $user_id has confirmed the email update request", $this->log_file);
      return $reset_email_is_valid;
    } catch(Exception $ex) {
      LogService::error('updateEmailConfirmed: '. $ex->getMessage(), $this->log_file);
      return null;
    }
  }

  /**
   * Update the user's email
   *
   * @param int $user_id
   * @param string $email
   * @return bool
  */
  private function updateEmail(int $user_id, string $email)
  {
    try {
      User::where('id', $user_id)
          ->update([
            'email' => $email
          ]);

      LogService::info("User $user_id has updated the email", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error('updateEmail: '. $ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Update the user's status
   *
   * @param int $user_id
   * @param int $status
   * @return bool
  */
  private function updateStatus(int $user_id, int $status)
  {
    try {
      if(!$this->isUserExists($user_id)) {
        throw new Exception('User not found');
      }

      User::where('id', $user_id)
          ->update([
            'status' => $status
          ]);

      LogService::info("User $user_id has updated the status", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error('updateStatus: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Get a single user
   *
   * @param int $user_id
   * @return object
  */
  public function getUser(int $user_id)
  {

  }
  
  /**
   * Get mulitple users
   *
   * @param int $records
   * @return object
  */
  public function getUsers(int $records = PaginationService::SMALL)
  {

  }

  /**
   * Set user role
   *
   * @param int $user_id
   * @param string $role
   * @return bool
   */
  public function setUserRole(int $user_id, string $role)
  {
    try {
      if(!$this->isUserExists($user_id)) {
        return false;
      }

      User::where('id', $user_id)->update([
        'role_id', Role::getRoleId($role)
      ]);

      LogService::info("User $user_id has updated the role", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error('setUserRole: '. $ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Get a single user by a dynamic field
   *
   * @param string $type
   * @param string $value
   * @return object|null
  */
  public function getUserByField(string $type, string $value)
  {
    return User::where($type, $value)->first();
  }
  
  /**
   * Create a user
   *
   * @param object $user
   * @param int $created_by
   * @return object|null
  */
  public function createUser(object $user, int $created_by)
  {
    try {
      $user = User::create([
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => $user->email,
        'role_id' => Role::getRoleId($user->role),
        'password' => $user->password,
        'status' => $user->status,
        'created_by' => $created_by
      ]);

      LogService::info("User $user->id has created by $created_by", $this->log_file);
      return $user;
    } catch(Exception $ex) {
      LogService::error('createUser: ' . $ex->getMessage(), $this->log_file);
      $this->deleteUser($user->id);
      return null;
    }
  }

  /**
   * Delete users
   *
   * @param array $user_ids
   * @return bool
  */
  public function deleteUsers(array $user_ids)
  {
    try {
      foreach($user_ids AS $user_id) {
        $this->deleteUser($user_id);
      }

      LogService::info('Finished to delete mulitple users', $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error('deleteUsers: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Start a delete request on a user
   * 
   *
   * @param object $user
   * @return bool
  */
  public function deleteUserRequest(object $user)
  {
    try {
      if(!$this->isUserExists($user->id)) {
        throw new Exception('User not found');
      }

      User::where('id', $user->id)->delete();

      $delete_user_request = DeleteAccount::create([
        'user_id' => $user->id,
        'status' => StatusService::PENDING,
        'token' => TokenService::createToken()
      ]);

      $data_to_send = (object) [
        'user_name' => $user->fullName(),
        'email' => $user->email,
        'token' => $delete_user_request->token
      ];
      
      MailService::send(DeleteAccountMail::class, $data_to_send, $user->email);

      LogService::info("User $user->id requested to delete account", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error('deleteUserRequest: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Respond the delete account request
   * A user can approve or disapprove this request
   *
   * @param string $email
   * @param string $token
   * @param int $status
   * @return bool
  */
  public function deleteUserResponse(string $email, string $token, int $status) :bool
  {
    try {
      $user = User::where('email', $email)->withTrashed()->first();
      if(!$user) {
        throw new Exception('User not found');
      }

      $delete_user_request_is_valid = DeleteAccount::where('user_id', $user->id)
                                                    ->where('token', $token)
                                                    ->where('status', StatusService::PENDING)
                                                    ->exists();
      if(!$delete_user_request_is_valid) {
        throw new Exception('User delete request is not found');
      }

      $this->updateDeleteAccount($user->id, $token, $status);
        
      if($status) {
        $this->deleteUser($user->id);
      } else {
        $this->undeleteUser($user->id);
      }

      LogService::info("The request to delete user $user->id is responded successfully with status: $status", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error('deleteUserResponse: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Update the request to delete a user 
   *
   * @param int $user_id
   * @param string $token
   * @param int $status
   * @return bool
   */
  private function updateDeleteAccount(int $user_id, string $token, int $status) :bool
  {
    return DeleteAccount::where('user_id', $user_id)
                        ->where('token', $token)
                        ->update([
                          'status' => $status,
                        ]);
  }
  
  /**
   * Undelete a user
   *
   * @param int $user_id
   * @return void
  */
  private function undeleteUser(int $user_id)
  {
    User::where('id', $user_id)
        ->update([
          'deleted_at' => null
        ]);
  }
  
  /**
   * Fully delete user from the application
   * Leave no trace for the user
   *
   * @param int $user_id
   * @return bool
   */
  private function deleteUser(int $user_id) :bool
  {
    try {
      $this->deleteUserMetaData($user_id);
      User::where('id', $user_id)->forceDelete();

      LogService::info("User $user_id is deleted successfully", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error('deleteUser: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Delete all of the user's meta data
   *
   * @param int $user_id
   * @return bool
   */
  private function deleteUserMetaData(int $user_id) :bool
  {
    try {
      UserDetail::where('user_id', $user_id)->delete();
      UserFriend::where('user_id', $user_id)->delete();
      DeleteAccount::where('user_id', $user_id)->delete();
      EmailVerification::where('user_id', $user_id)->delete();
      ResetEmail::where('user_id', $user_id)->delete();
      ResetPassword::where('user_id', $user_id)->delete();
      UserFollowStudio::where('user_id', $user_id)->delete();

      $studioService = new StudioService;
      $studioService->userDeleted($user_id);

      $tattooService = new TattooService;
      $tattooService->userDeleted($user_id);

      return true;
    } catch(Exception $ex) {
      LogService::error('deleteUserMetaData: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Checks if the user exists
   *
   * @param int $user_id
   * @return bool
  */
  public function isUserExists(int $user_id)
  {
    return User::where('id', $user_id)->exists();
  }
  
  /**
   * Get the user friends
   * Can be filtered by status
   *
   * @param int $user_id
   * @param int $records
   * @param int $status
   * @return object
  */
  public function getUserFriends(int $user_id, int $records = PaginationService::SMALL, int $status = StatusService::ACTIVE)
  {
    try {
      $user_friends = UserFriend::where('id', $user_id)
                                ->where('status', $status)
                                ->with('user')
                                ->simplePaginate($records);
    
      return $user_friends;
    } catch(Exception $ex) {
      LogService::error('getUserFriends: ' . $ex->getMessage(), $this->log_file);
      return null;
    }
  }
  
  /**
   * Send a user a friend request
   *
   * @param int $user_id
   * @param int $friend_id
   * @return object
  */
  public function sendFriendRequest(int $user_id, int $friend_id)
  {
    try {
      $user_friend = UserFriend::create([
        'user_id' => $user_id,
        'friend_id' => $friend_id,
        'status' => StatusService::PENDING
      ]);

      LogService::info("User $user_id sent a friend request to user $friend_id", $this->log_file);
      return $user_friend;
    } catch(Exception $ex) {
      LogService::error('sendFriendRequest: ' . $ex->getMessage(), $this->log_file);
      return null;
    }
  }

  /**
   * Update the status of a friend request
   *
   * @param int $id
   * @param int $status
   * @param int $user_id
   * @return object|null
  */
  public function updateFriendRequest(int $id, int $status, int $user_id)
  {
    try {
      $friend_request = UserFriend::find($id);
      if(!$friend_request) {
        throw new Exception('Friend request not found');
      }

      $friend_request->status = $status;
      $friend_request->updated_at = now();
      $friend_request->save();

      LogService::info("User $user_id updated the friend request $id to status $status", $this->log_file);
      return $friend_request;
    } catch(Exception $ex) {
      LogService::error('updateFriendRequest: ' . $ex->getMessage(), $this->log_file);
      return null;
    }
  }

  /**
   * Update users status
   *
   * @param array $user_ids
   * @param int $status
   * @param int $created_by
   * @return bool
  */
  public function updateUsersStatus(array $user_ids, int $status, int $created_by)
  {
    try {
      User::whereIn('id', $user_ids)
        ->update([
          'status', $status
        ]);

      LogService::info("User $created_by updated the users " . json_encode($user_ids) . " status to $status", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error('updateUsersStatus: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Update a user's password
   *
   * @param int $user_id
   * @param string $new_password
   * @param int $created_by
   * @return bool
  */
  public function updateUserPassword(int $user_id, string $new_password, int $created_by)
  {
    try {
      if(!$this->isUserExists($user_id)) {
        throw new Exception('User not found');
      }

      $password_set_successfully = $this->setUserPassword($user_id, $new_password);
      if(!$password_set_successfully) {
        throw new Exception('Failed to update the password');
      }

      LogService::info("User $created_by updated the user $user_id password", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error('updateUserPassword: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Update users status
   *
   * @param string $old_password
   * @param string $new_password
   * @param object $user
   * @return bool
  */
  public function changeSelfPassword(string $old_password, string $new_password, object $user)
  {
    try {
      if (!Hash::check($old_password, $user->password)) {
        throw new Exception('Old password is incorrect');
      }
  
      $password_set_successfully = $this->setUserPassword($user->id, $new_password);
      if(!$password_set_successfully) {
        throw new Exception('Failed to update the password');
      }

      LogService::info("User $user->id updated the his password", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error('changeSelfPassword: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Set a user password
   *
   * @param int $user_id
   * @param string $new_password
   * @return bool
  */
  private function setUserPassword(int $user_id, string $new_password)
  {
    try {
      User::where('id', $user_id)->update([
        'password' => bcrypt($new_password)
      ]);

      LogService::info("User $user_id password has updated", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error('setUserPassword: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Get the user tattoos
   *
   * @param int $user_id
   * @param App\Domain\Tattoos\Services\TattooService $tattooService
   * @param int $records
   * @return object|null
  */
  public function getUserTattoos(int $user_id, object $tattooService, int $records = PaginationService::SMALL)
  {
    try {
      $user_tattoos = $tattooService->getTattoosByUser($user_id, $records, StatusService::ACTIVE);

      return $user_tattoos;
    } catch(Exception $ex) {
      LogService::error('getUserTattoos: ' . $ex->getMessage(), $this->log_file);
      return null;
    }
  }
  
  /**
   * Get the user followed studios
   *
   * @param int $user_id
   * @param int $records
   * @return object|null
  */
  public function getUserFollowedStudios(int $user_id, int $records = PaginationService::SMALL)
  {
    try {
      $user_followed_studios = UserFollowStudio::where('user_id', $user_id)
                                               ->with('studio')
                                               ->simplePaginate($records);

      return $user_followed_studios;
    } catch(Exception $ex) {
      LogService::error('getUserFollowedStudios: ' . $ex->getMessage(), $this->log_file);
      return null;
    }
  }
  
  /**
   * Check if the user saved the tattoo
   *
   * @param int $user_id
   * @param int $tattoo_id
   * @return bool
   */
  public function isUserSavedTattoo(int $user_id, int $tattoo_id)
  {
    try {
      return TattooSave::where('user_id', $user_id)
                      ->where('tattoo_id', $tattoo_id)
                      ->exists();
    } catch(Exception $ex) {
      LogService::error('isUserSavedTattoo: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }

  /**
   * Get the user saved tattoos
   *
   * @param int $user_id
   * @param int $records
   * @return object|null
  */
  public function getUserSavedTattoos(int $user_id, int $records)
  {
    try {
      $user_saved_tattoos = TattooSave::where('user_id', $user_id)
                                      ->with('tattoo')
                                      ->simplePaginate($records);

      return $user_saved_tattoos;
    } catch(Exception $ex) {
      LogService::error('getUserSavedTattoos: ' . $ex->getMessage(), $this->log_file);
      return null;
    }
  }
  
  /**
   * User forgot a password
   * Sends an email to reset password
   *
   * @param string $email
   * @return bool
  */
  public function forgotPassword(string $email) :bool
  {
    try {
      $reset_password = ResetPassword::create([
        'email' => $email,
        'token' => TokenService::createToken(),
        'created_at' => now()
      ]);

      $data_to_send = (object) [
        'token' => $reset_password->token
      ];

      MailService::send(ForgotPasswordMail::class, $data_to_send, $email);

      return true;
    } catch(Exception $ex) {
      LogService::error('forgotPassword: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Reset the user password after forgot
   * 
   * @param string $password
   * @param string $token
   * @param string $email
   * @return bool
  */
  public function resetPassword(string $password, string $token, string $email)
  {
    try {
      $reset_password = ResetPassword::where('token', $token)
                                     ->where('email', $email)
                                     ->whereNull('reseted_at')
                                     ->where('created_at', '>', Carbon::now()->subMinutes(ResetPassword::RESET_TIME)->toDateTimeString())
                                     ->orderBy('created_at', 'desc')
                                     ->first();

      if(!$reset_password) {
        throw new Exception('Unabled to reset password, no record was found');
      }

      $user = $this->getUserByField('email', $email);
      if(!$user) {
        throw new Exception('User not found');
      }

      $reset_password->reseted_at = now();
      $reset_password->save();

      $this->setUserPassword($user->id, $password);
      
      LogService::error("User $user->id has reset his password", $this->log_file);
      return true;
    } catch(Exception $ex) {
      LogService::error('resetPassword: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Check if the user watched the tattoo
   *
   * @param int $user_id
   * @param int $tattoo_id
   * @return bool
   */
  public function isUserWatchedTattoo(int $user_id, int $tattoo_id)
  {
    try {
      return TattooWatch::where('user_id', $user_id)
                      ->where('tattoo_id', $tattoo_id)
                      ->exists();
    } catch(Exception $ex) {
      LogService::error('isUserWatchedTattoo: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Check if the user watched the studio
   *
   * @param int $user_id
   * @param int $studio_id
   * @return bool
   */
  public function isUserWatchedStudio(int $user_id, int $studio_id)
  {
    try {
      return StudioWatch::where('user_id', $user_id)
                        ->where('studio_id', $studio_id)
                        ->exists();
    } catch(Exception $ex) {
      LogService::error('isUserWatchedStudio: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Get the user watched tattoos
   *
   * @param int $user_id
   * @param int $records
   * @return object|null
   */
  public function getUserWatchedTattoos(int $user_id, int $records = PaginationService::SMALL)
  {
    try {
      $watched_tattoos = TattooWatch::where('user_id', $user_id)
                                    ->with('tattoo')
                                    ->simplePaginate($records);

      return $watched_tattoos;
    } catch(Exception $ex) {
      LogService::error('getUserWatchedTattoos: ' . $ex->getMessage(), $this->log_file);
      return null;
    }
  }

  /**
   * Check if the user liked the tattoo
   *
   * @param int $user_id
   * @param int $tattoo_id
   * @return bool
   */
  public function isUserLikedTattoo(int $user_id, int $tattoo_id)
  {
    try {
      return TattooLike::where('user_id', $user_id)
                        ->where('tattoo_id', $tattoo_id)
                        ->exists();
    } catch(Exception $ex) {
      LogService::error('isUserLikedTattoo: ' . $ex->getMessage(), $this->log_file);
      return false;
    }
  }
  
  /**
   * Get the user liked tattoos
   *
   * @param int $user_id
   * @param int $records
   * @return object|null
   */
  public function getUserLikedTattoo(int $user_id, int $records = PaginationService::SMALL)
  {
    try {
      $liked_tattoos = TattooLike::where('user_id', $user_id)
                                ->with('tattoo')
                                ->simplePaginate($records);

      return $liked_tattoos;
    } catch(Exception $ex) {
      LogService::error('getUserLikedTattoo: ' . $ex->getMessage(), $this->log_file);
      return null;
    }
  }
}