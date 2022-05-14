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
use App\Domain\Users\Models\UserCourse;
use App\Domain\Users\Models\UserDetail;
use App\Domain\Orders\Services\OrderService;
use App\Events\Users\UserResetPasswordEvent;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Users\Models\UserCourseLesson;
use App\Domain\Users\Models\UserResetPassword;
use App\Domain\Content\Services\ContentService;
use App\Domain\Support\Services\SupportService;
use App\Domain\Users\Models\UserEmailVerification;
use App\Domain\Users\Models\UserCourseLessonWatch;

class UserService
{  
  /**
   * @var ContentService
  */
  private $content_service;
  
  /**
   * @var SupportService
  */
  private $support_service;
  
  /**
   * @var OrderService
  */
  private $order_service;
  
  /**
   * @var LogService
  */
  private $log_service;
  
  /**
   * @var UserCours|null
  */
  private $user_course;
    
  /**
   * @param ContentService $content_service
   * @param SupportService $support_service
   * @param OrderService $order_service
   * @return void
  */
  public function __construct(ContentService $content_service = null, SupportService $support_service = null, OrderService $order_service = null)
  {
    $this->content_service  = $content_service;
    $this->support_service  = $support_service;
    $this->order_service    = $order_service;
    $this->log_service      = new LogService('users');
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
              ->get();
  }
  
  /**
   * @param int $user_id
   * @return User|null
  */
  public function getUserByID(int $user_id): ?User
  {
    return User::find($user_id);
  }
  
  /**
   * @param string $email
   * @return null|User
  */
  public function getUserByEmail(string $email): ?User
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
   * @param Object $user
   * @return string
  */
  public function getFullName(Object $user): string
  {
    return $user->first_name . ' ' . $user->last_name;
  }
  
  /**
   * @param Object $user
   * @param int $status
   * @return Collection|null
  */
  public function getUserCourses(Object $user, int $status = null): ?Collection
  {
    $user_courses = UserCourse::where('user_id', $user->id);
    if(!is_null($status)) {
      $user_courses = $user_courses->where('status', $status);
    }
    
    $user_courses = $user_courses->select('id', 'course_id', 'progress')->pluck('course_id');
    $courses      = $this->content_service->getCoursesFullContent($user_courses->toArray());

    return $courses;
  }
  
  /**
   * @param Object $user
   * @return User
  */
  public function getProfile(Object $user): User
  {
    $user = $user->load('details');

    unset($user->status);
    unset($user->updated_at);
    unset($user->created_at);
    unset($user->created_by);
    unset($user->deleted_at);

    return $user;
  }
  
  /**
   * @param int $user_id
   * @param int $content_id
   * @return UserCourse|null
  */
  public function assignCourseToUser(int $user_id, int $content_id): ?UserCourse
  {
    try {
      $user_course_service = new UserCourseService();
      return $user_course_service->assignCourseToUser($user_id, $content_id);
    } catch(Exception $ex) {
      $this->log_service->error($ex);
      return null;
    }
  }
  
  /**
   * @param Object $user
   * @return Collection
  */
  public function getUserSupportTickets(Object $user): Collection
  {
    return $this->support_service->getTicketsByUsers($user->id);
  }
  
  /**
   * @param array $data
   * @param int $user_id
   * @return Collection
  */
  public function setLessonProgress(array $data, int $user_id)
  {
    if(!$this->hasAccessToLesson($user_id, $data['lesson_id'])) {
      throw new Exception('User doesn\'t have access to the lesson: ' . $data['lesson_id']);
    }

    $video = $this->content_service->getVideoByLessonId($data['lesson_id']);
    $end_time = $data['end_time'] > $video->video_length ? $video->video_length : $data['end_time'];
    $progress = $this->calcVideoProgress($video->video_length, $end_time);

    if($user_lesson = $this->getUserLesson($user_id, $data['lesson_id'])) {
      $user_lesson = $this->updateLessonProgress($user_lesson, $progress, $user_id);
    } else {
      $user_lesson = $this->createLessonProgress($data['lesson_id'], $user_id, $progress);
    }

    $this->setLessonWatchRecord($user_lesson, $data['start_time'], $end_time);

    $course_id = $this->content_service->getLessonCourseId($data['lesson_id']);
    $this->updateUserCourseProgress($course_id, $user_lesson->user_course_id);

    return $user_lesson;
  }
  
  /**
   * @param int $user_id
   * @param int $lesson_id
   * @return UserCourseLesson|null
  */
  public function getUserLesson(int $user_id, int $lesson_id): ?UserCourseLesson
  {
    $user_course = $this->getUserActiveCourseByLesson($user_id, $lesson_id);
    return UserCourseLesson::where('course_lesson_id', $lesson_id)
                          ->where('user_id', $user_id)
                          ->where('user_course_id', $user_course->id)
                          ->first();
  }

  /**
   * @param int $user_id
   * @param int $lesson_id
   * @return UserCourse|null
  */
  public function getUserActiveCourseByLesson(int $user_id, int $lesson_id): ?UserCourse
  {
    return UserCourse::where('user_id', $user_id)
                      ->where('user_courses.status', StatusService::ACTIVE)
                      ->join('course_lessons', 'course_lessons.course_id', 'user_courses.course_id')
                      ->where('course_lessons.id', $lesson_id)
                      ->select('user_courses.*')
                      ->first();
  }
  
  /**
   * @param Object $user
   * @return Array
  */
  public function getUserProgress(Object $user): array
  {
    $user_progress = [];
    $user_progress['courses'] = UserCourse::where('user_id', $user->id)
                    ->where('status', StatusService::ACTIVE)
                    ->with('lessonsProgress')
                    ->select('id', 'course_id', 'progress')
                    ->get();
                    
    // $user_progress['last_active_lesson'] = $this->getUserLastActiveLesson($user_progress['courses']);
    $user_progress['last_active_lesson'] = $user->load('lastActiveLesson')->lastActiveLesson;

    return $user_progress;
  }
  
  /**
   * @param Object $user
   * @return Collection
  */
  public function getUserOrders(Object $user): Collection
  {
    $orders = $this->order_service->getOrdersByUsers($user->id);
    $courses = $this->content_service->getCoursesFullContent($orders->pluck('content_id')->toArray());
    
    foreach($orders AS $order)
    {
      $order->course = $courses->where('id', $order->content_id)->first();
    }

    return $orders;
  }

  /**
   * @param User $user
   * @return void
  */
  public function logout(User $user)
  {
    $user->token()->revoke();
    $this->log_service->info('User ' . $user->id . ' logged out successfully');
  }
  
  /**
   * @param array $data
   * @return User|null
  */
  public function signup(array $data): ?User
  {
    try {
      $user             = new User();
      $user->role_id    = Role::NORMAL;
      $user->status     = StatusService::PENDING;
      $user->email      = $data['email'];
      $user->password   = bcrypt($data['password']);
      $user->save();

      $this->log_service->info('User ' . $user->id . 'completed sign up, part 1');
      $data['user_id'] = $user->id;
      $this->createUserDetails($data);
      $this->log_service->info('User ' . $user->id . 'completed sign up, part 2');
      $this->saveEmailVerification($user, $user->email);
      $this->log_service->info('User ' . $user->id . 'completed sign up, part 3');
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
   * @param array $data
   * @param int|null $created_by
   * @return User|null
  */
  public function createUserByAdmin(array $data, ?int $created_by): ?User
  {
    try {
      $user             = new User();
      $user->role_id    = $data['role_id'];
      $user->status     = StatusService::PENDING;
      $user->email      = $data['email'];
      $user->password   = bcrypt($data['password']);
      $user->created_by = $created_by;
      $user->save();

      $data['user_id']    = $user->id;
      $this->log_service->info('User ' . $user['id'] . ' has been created by ' . $created_by);
      $this->createUserDetails($data, $created_by);

      return $user;
    } catch(Exception $ex) {
      if(isset($user) && $user) {
        $this->deleteUser($user->id);
      }

      throw $ex;
    }
  }
    
  /**
   * @param int $user_id
   * @param int|null $updated_by
   * @return User|null
  */
  public function activateUser(int $user_id, ?int $updated_by = null)
  {
    return $this->saveStatus($user_id, StatusService::ACTIVE);
  }
    
  /**
   * Update user by an admin
   *
   * @param array $data
   * @param int|null $updated_by
   * @return User|null
  */
  public function updateUser(array $data, ?int $updated_by)
  {
    $user             = User::find($data['id']);
    $user->role_id    = Role::ROLES_LIST[strtolower($data['role'])];
    $user->status     = StatusService::PENDING;
    $user->email      = $data['email'];
    $user->password   = bcrypt($data['password']);
    $user->updated_by = $data['updated_by'];
    $user->save();

    $this->log_service->info('User ' . $data['id'] . ', main data was updated by ' . $updated_by);

    $this->updateUserDetails($data, $updated_by);

    return $data;
  }
    
  /**
   * @param array $data
   * @param null $user_id
   * @return User|null
  */
  public function updateProfile(array $data, int $user_id)
  {
    $user = UserDetail::where('user_id', $user_id)->first();
    $user->first_name = $data['first_name'];
    $user->last_name  = $data['last_name'];
    $user->phone      = $data['phone'];
    $user->gender     = $data['gender'];
    $user->save();

    $this->log_service->info('User ' . $user_id . ', updated his profile');

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
   * @param array $data
   * @param int $updated_by
   * @return UserDetail|null
   */
  public function updateUserDetails(array $data, int $updated_by): ?UserDetail
  {
    if(!$user_details = UserDetail::where('user_id', $data['user_id'])->first()) {
      throw new Exception('Failed to find user');
    }

    $this->saveUserDetails($user_details, $data);
    $this->log_service->info('User ' . $data['user_id'] . ', details were updated by ' . $updated_by);
    
    return $user_details;
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
                                     ->where('created_at', '>=', Carbon::now()->subDay()->toDateTimeString())
                                     ->first();
  
    if(!$reset_password_request) {
      throw new Exception('Reset Password request not found');
    }
    
    $user = $this->getUserByEmail($email);
    if(!$user) {
      throw new Exception('User not found');
    }
    
    $this->savePassword($user, $password);
    
    $this->log_service->info('User has reset his password successfully');
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
    if(!$user = $this->getUserByEmail($email)) {
      $this->log_service->error('Email does not exists');
      return;
    }
    
    if(!$this->canResetPassword($email)) {
      $this->log_service->error("Email $email have reached maximum forgot reset attempts");
      return;
    }

    $this->deactivateUsersResetPasswords($email);

    $forgot_password_request = UserResetPassword::create([
      'token'     => Str::random(50),
      'email'     => $email,
      'status'    => StatusService::PENDING,
      'created_at' => now()
    ]);
    
    $forgot_password_request->user_name = $user->details->first_name;
    $this->log_service->info("Submitted a forgot password request for user $user->id");

    event(new UserResetPasswordEvent($forgot_password_request));
  }
  
  /**
   * @param string $email
   * @return int
  */
  public function deactivateUsersResetPasswords(string $email): int
  {
    $records_updated =  UserResetPassword::where('email', $email)
                                         ->where('status', StatusService::PENDING)
                                         ->update(['status' => StatusService::INACTIVE]);

    $this->log_service->info("Deactivate $records_updated reset passwords");
    return $records_updated;
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

    $this->savePassword($user, $new_password);
    $this->log_service->info('Password has been changed successfully');
  }
  
  /**
   * @param array $data
   * @param int $updated_by
   * @return bool
  */
  public function updateUserEmail(array $data, int $updated_by): bool
  {
    $user = User::where('id', $data['id'])->update(['email' => $data['email']]);
    $this->log_service->info("User $updated_by updated the email of user " . $data['id']);
    return $user;
  } 
  
  /**
   * @param array $data
   * @param int $updated_by
   * @return bool
  */
  public function updateUserPassword(array $data, int $updated_by): bool
  {
    $result = $this->savePassword($this->getUserById($data['id']), $data['password']);
    $this->log_service->info("User $updated_by updated the password of user " . $data['id']);
    return $result;
  } 
   
  /**
   * @param User $user
   * @param string $email
   * @return void
  */
  public function changeEmail(User $user, string $email)
  {
    if($user->email === $email) {
      $this->log_service->info('User failed to change email, attempted his current email');
      return;
    }
    
    if(User::where('email', $email)->exists()) {
      $this->log_service->info('User failed to change email, attempted an existing email');
      return;
    }

    if($this->userHasOpenEmailVerificationRequest($user->id)) {
      $this->log_service->info('User already has an open email verification request');
      return;
    }

    $this->saveEmailVerification($user, $email);
  }
  
  /**
   * @param int $user_id
   * @return bool
  */
  public function userHasOpenEmailVerificationRequest(int $user_id): bool
  {
    return UserEmailVerification::where('user_id', $user_id)->whereNotNull('verified_at');
  }
    
  /**
   * @param string $email
   * @param string $token
   * @return Void
  */
  public function verifyEmail(string $email, string $token)
  {
    $verification = UserEmailVerification::where('email', $email)
                                        ->where('token', $token)
                                        ->first();
    if(!$verification) {
      $this->log_service->info("Failed to verify the email with the token: $token");
      throw new Exception('Failed to verify email');
    }

    if($verification->verified_at) {
      return;
    }
    
    $user = $this->getUserByEmail($email);
    
    $this->log_service->info('User ' . $user->id . ' has verified his email');
    $verification->update(['verified_at' => now()]);
    $this->updateUserEmail([
      'id'    => $verification->user_id,
      'email' => $email 
    ], $verification->user_id);
    $this->saveStatus($verification->user_id, StatusService::ACTIVE);
  }
  
  /**
   * @param User $user
   * @param string $email
   * @return UserEmailVerification
  */
  private function saveEmailVerification(User $user, string $email): UserEmailVerification
  {
    $email_verification = UserEmailVerification::create([
      'user_id' => $user->id,
      'email' => $email,
      'token' => Str::random(50),
      'created_at' => now()
    ]);

    $this->log_service->info('Sent verification mail for changing the email');

    event(new UserCreatedEvent($user));
    return $email_verification;
  }

  /**
   * @param int $user_id
   * @param int $status
   * @return bool
  */
  private function saveStatus(int $user_id, int $status): bool
  {
    $result = User::where('id', $user_id)->update(['status' => $status]);
    $this->log_service->info('User ' . $user_id . ' status has been updated to ' . $status);
    return $result;
  }
  
  /**
   * @param User $user
   * @param string $password
   * @return bool
  */
  private function savePassword(User $user, string $password): bool
  {
    if($this->isNewPasswordMatchesOldPassword($user->password, $password)) {
      throw new Exception('Can\'t update new password that matches the old password');
    }
    
    return User::where('id', $user->id)->update(['password' => bcrypt($password)]);
  }
  
  /**
   * @param string $current_password
   * @param string $new_password
   * @return bool
  */
  private function isNewPasswordMatchesOldPassword(string $current_password, string $new_password): bool
  {
    return Hash::check($new_password, $current_password);
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
   * @param array $data
   * @param int|null $created_by
   * @return UserDetail
  */
  private function createUserDetails(array $data, ?int $created_by = null): UserDetail
  {
    $user_details             = new UserDetail();
    $user_details->user_id    = $data['user_id'];
    $this->saveUserDetails($user_details, $data);
    $this->log_service->info('User ' . $data['user_id'] . ', details were created by ' . $created_by ?? $data['user_id']);
    
    return $user_details;
  }
  
  /**
   * @param UserDetail $user_details
   * @param array $data
   * @return UserDetail
  */
  private function saveUserDetails(UserDetail $user_details, array $data): UserDetail
  {
    $user_details->first_name  = $data['first_name'];
    $user_details->last_name   = $data['last_name'];
    $user_details->phone       = $data['phone'] ?? null;
    $user_details->gender      = $data['gender'] ?? null;
    $user_details->birth_date  = $data['birth_date'] ?? null;
    $user_details->save();

    return $user_details;
  }
  
  /**
   * @param int $user_id
   * @param int $lesson_id
   * @return bool
  */
  private function hasAccessToLesson(int $user_id, int $lesson_id): bool
  {
    $course_ID = $this->content_service->getLessonCourseId($lesson_id);

    $this->user_course = UserCourse::query()
                        ->where('user_id', $user_id)
                        ->where('course_id', $course_ID)
                        ->where('status', StatusService::ACTIVE)
                        ->first();

    return !!$this->user_course;
  }
  
  /**
   * @param UserCourseLesson $user_lesson
   * @param int $progress
   * @param int $user_id
   * @return UserCourseLesson
  */
  private function updateLessonProgress(UserCourseLesson $user_lesson, int $progress, int $user_id): UserCourseLesson
  {
    if($user_lesson->progress >= $progress) {
      return $user_lesson;
    }

    $user_lesson->update([
      'progress'    => $progress,
      'finished_at' => $progress === 100 ? now() : null,
      
    ]);

    return $user_lesson;
  }

  /**
   * @param int $lesson_id
   * @param int $user_id
   * @param int $progress
   * @return UserCourseLesson
  */
  private function createLessonProgress(int $lesson_id, int $user_id, int $progress): UserCourseLesson
  {
    $user_lesson                    = new UserCourseLesson;
    $user_lesson->user_course_id    = $this->user_course->id;
    $user_lesson->course_lesson_id  = $lesson_id;
    $user_lesson->user_id           = $user_id;
    $user_lesson->progress          = $progress;
    $user_lesson->created_at        = now();

    if($progress === 100) {
      $user_lesson->finished_at     = now();
    }
    
    $user_lesson->save();  

    return $user_lesson;
  }
  
  /**
   * write a record for the user watching time in a lesson
   * with that we can analyze the most popular time each lesson
   *
   * @param UserCourseLesson $user_lesson
   * @return void
  */
  private function setLessonWatchRecord(UserCourseLesson $user_lesson, float $start_time, float $end_time)
  {
    UserCourseLessonWatch::create([
      'user_course_lesson_id' => $user_lesson->id,
      'course_lesson_id'      => $user_lesson->course_lesson_id,
      'user_id'               => $user_lesson->user_id,
      'start_time'            => $start_time,
      'end_time'              => $end_time,
      'created_at'            => now()
    ]);
  }
  
  /**
   * @param float $video_length
   * @param float $end_time
   * @return int
  */
  private function calcVideoProgress(float $video_length, float $end_time): int
  {
    $progress = (int) ($end_time * 100) / $video_length;
    if($progress > 100 || $progress >= 95) {
      return 100;
    }

    return $progress;
  }
  
  /**
   * @param int $course_id
   * @param int $user_course_id
   * @return void
  */
  private function updateUserCourseProgress(int $course_id, int $user_course_id)
  {
    try {
      $lessons                = $this->content_service->getLessonsDurationByCourseId($course_id);
      $lessons_durations      = $lessons->pluck('video_length')->toArray();
      $total_course_duration  = array_sum($lessons_durations);
      $user_total_viewed_time = 0;
      
      $user_lessons_progress = UserCourseLesson::where('user_course_id', $user_course_id)
                                                ->select('course_lesson_id', 'progress')
                                                ->get();

      for($course_lesson_index = 0; $course_lesson_index < count($lessons); $course_lesson_index++) {
        $lesson = $lessons[$course_lesson_index];
        
        for($user_lesson_index = 0; $user_lesson_index < count($user_lessons_progress); $user_lesson_index++) {
          $user_lesson = $user_lessons_progress[$user_lesson_index];
          
          if($lesson->id === $user_lesson->course_lesson_id) {
            // calc the user progress in that lesson
            $user_total_viewed_time += $lesson->video_length * ( $user_lesson->progress / 100 );
            break;
          }
          
        }
        
      }
      
      $user_course_progress = $user_total_viewed_time * 100 / $total_course_duration;
      UserCourse::where('id', $user_course_id)->update([
        'progress' => $user_course_progress
      ]);
    } catch(Exception $ex) {
      $this->log_service->error($ex);
    }
  }
  
  /**
   * Get the last unfinished lesson
   *
   * @param Collection $courses
   * @return object|null
  */
  private function getUserLastActiveLesson(Collection $courses): ?object
  {
    $last_active_lesson = null;

    foreach($courses AS $course) {
      foreach($course->lessonsProgress AS $lesson) {
        if(!$last_active_lesson || ($lesson->finished_at > $last_active_lesson->finished_at)) {
          $last_active_lesson = $lesson;
        }
      }
    }

    return $last_active_lesson;
  }

}