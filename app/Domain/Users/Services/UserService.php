<?php

namespace App\Domain\Users\Services;

use Exception;
use App\Domain\Users\Models\Role;
use App\Domain\Users\Models\User;
use App\Domain\Helpers\LogService;
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
   * Signup a user
   *
   * @param array $user
   * @return object|null
  */
  public function signup(array $user)
  {
    try {
      $new_user = new User;
      $new_user->first_name = $user['first_name'];
      $new_user->last_name = $user['last_name'];
      $new_user->email = $user['email'];
      $new_user->role_id = Role::NORMAL;
      $new_user->password = bcrypt($user['password']);
      if(!empty($user['phone'])) {
        $new_user->phone =  $user['phone'];
      }
      $new_user->save();

      $this->createUserDetails($new_user->id);
      $this->sendEmailVerification($new_user);

      LogService::info("User $new_user->id signup successfully", $this->log_file);
      return $new_user;
    } catch(Exception $ex) {
      LogService::error(__METHOD__ . ' --- ' . $ex->getMessage(), $this->log_file);
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
      LogService::error(__METHOD__ . ' --- ' . $ex->getMessage(), $this->log_file);
      return false;
    }   
  }
  
  /**
   * check if the user's email is verified
   *
   * @param object $user
   * @return bool
   */
  public function isEmailVerified(object $user) :bool
  {
    $email_verifications = UserAction::where('token', $data->token)
                                      ->where('user_id', $data->user_id)
                                      ->where('action_type_id', $data->action_type_id)
                                      ->get();
  }
  
  /**
   * Create an email verification record and sends a verification mail
   *
   * @param object $user
   * @return bool
   */
  public function sendEmailVerification(object $user) :bool
  {
    try {
      $this->setUserAction(
        LuUserActionType::VERIFY_EMAIL,
        $user
      );

      return true;
    } catch(Exception $ex) {
      LogService::error(__METHOD__ . ' --- ' . $ex->getMessage(), $this->log_file);
      return false;
    }   
  }

  /**
   * Verify user action
   *
   * @param object $data
   * @return bool
  */
  public function verifyUserAction(object $data) :bool
  {
    try {
      $user_action = UserAction::where('token', $data->token)
                               ->where('user_id', $data->user_id)
                               ->where('action_type_id', $data->action_type_id)
                               ->first();

      if(!$user_action) {
        throw new Exception('User action not found');
      }

      $user_action->verified_at = now();
      $user_action->save();

      // save the new email / password / etc..

      return true;
    } catch(Exception $ex) {
      LogService::error(__METHOD__ . ' --- ' . $ex->getMessage(), $this->log_file);
      return false;
    }   
  }
  
  /**
   * Create a user action, the action is entered into queue
   *
   * @param string $action_type_id
   * @param object $user
   * @return void
   */
  private function setUserAction(string $action_type_id, object $user)
  {
    $data = (object) [
      'action_type_id' => $action_type_id,
      'user_id' => $user->id,
      'token' => TokenService::createToken(),
    ];

    UserAction::create($data);

    MailService::send(EmailVerificationMail::class, $data, $user->email);
  }
}