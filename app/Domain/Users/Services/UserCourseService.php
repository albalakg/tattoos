<?php

namespace App\Domain\Users\Services;

use Exception;
use Illuminate\Support\Carbon;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\MailService;
use App\Domain\Helpers\StatusService;
use App\Mail\User\AddCourseToUserMail;
use App\Domain\Users\Models\UserCourse;
use App\Mail\Tests\TestStatusUpdateMail;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Users\Models\UserCourseLesson;
use App\Events\Users\UserCourseDisabledEvent;
use App\Domain\Content\Services\CourseService;
use App\Domain\Users\Models\UserCourseSchedule;
use App\Domain\Users\Models\UserCourseScheduleLesson;
use App\Domain\Users\Models\UserCourseSubmission;
use App\Domain\Users\Models\UserCourseSubmissionComment;

class UserCourseService
{
  private LogService $log_service;

  private ?CourseService $course_service;

  private ?UserService $user_service;

  private ?MailService $mail_service;

  const DEFAULT_USER_COURSE_PERIOD = 12; // in months

  public function __construct(
    CourseService $course_service = null, 
    UserService $user_service = null,
    MailService $mail_service = null,
  )
  {
    $this->course_service = $course_service;
    $this->user_service   = $user_service;
    $this->mail_service   = $mail_service;
    $this->log_service    = new LogService('userCourses');
  }
  
  /**
   * @return Collection
  */
  public function getAllTests(): Collection
  {
    $tests = UserCourseSubmission::query()
            ->with('comments', 'userCourse')
            ->select(
              'id',
              'user_course_id',
              'video',
              'status',
              'comment',
              'created_at',
              'finished_at',
              'updated_at'
            )
            ->orderBy('id', 'desc')
            ->get();

    foreach($tests AS $test) {
      foreach($test->comments AS  $comment) {
        $comment->human_time = $comment->created_at->diffForHumans();
      }
    }

    return $tests;
  }
  
  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return UserCourse::query()
              ->select(
                'id',
                'course_id',
                'user_id',
                'progress',
                'status',
                'end_at',
                'created_at',
                'created_by',
              )
              ->withCount('finishedLessons')
              ->orderBy('id', 'desc')
              ->get();
  }
  
  /**
   * @param int $user_id
   * @return Collection
  */
  public function getActiveCourseByUserID(int $user_id): Collection
  {
    return UserCourse::where('user_id', $user_id)
                     ->where('status', StatusService::ACTIVE)
                     ->with('fullCourse')
                     ->select('course_id', 'progress')
                     ->get();
  }
  
  /**
   * @param int $course_id
   * @return Collection
  */
  public function getActiveCourseUsersWithoutSchedule(int $course_id): Collection
  {
    $user_courses = UserCourse::where('user_courses.course_id', $course_id)
                              ->where('user_courses.status', StatusService::ACTIVE)
                              ->select('user_courses.user_id', 'user_courses.id')
                              ->get();

    $user_courses_with_schedule = UserCourseSchedule::whereIn('user_course_id', $user_courses->pluck('id')->toArray())
                                                    ->pluck('user_course_id')
                                                    ->toArray();
    
    foreach($user_courses AS $index => $user_course) {
      if(in_array($user_course->id, $user_courses_with_schedule)) {
        unset($user_courses[$index]);
      }
    }

    return $user_courses;
  }
  
  /**
   * Check if there are active users in the course
   *
   * @param int $course_id
   * @return bool
  */
  public function isCourseInUsed(int $course_id) :bool
  {
    return UserCourse::where('course_id', $course_id)->where('status', StatusService::ACTIVE)->exists();
  }
  
  /**
   * @param int $id
   * @param int $status
   * @param int $updated_by
   * @return void
  */
  public function updateTestStatus(int $id, int $status, int $updated_by)
  {
    if(!$comment = UserCourseSubmission::find($id)) {
      throw new Exception("Test $id not found");
    }

    $comment->update(['status' => $status]);
    $comment->load('user');
    $comment->user_name = $this->user_service->getFullName($comment->user);

    $mail_service = new MailService;
    $mail_service->delay()->send(
      $comment->user->email,
      TestStatusUpdateMail::class,
      $comment
    );
  }
  
  /**
   * @param int $user_course_submission_id
   * @param string $comment
   * @param int $created_by
   * @return UserCourseSubmissionComment
  */
  public function createComment(int $user_course_submission_id, string $comment, int $created_by): UserCourseSubmissionComment
  {
    $new_comment                            = new UserCourseSubmissionComment;
    $new_comment->user_course_submission_id = $user_course_submission_id;
    $new_comment->comment                   = $comment;
    $new_comment->created_at                = now();
    $new_comment->created_by                = $created_by;
    $new_comment->save();

    $new_comment->load('user');
    $mail_service = new MailService;
    $mail_service->delay()->send(
      $new_comment->user->email,
      NewTestCommentMail::class,
      [
        'user_name'   => $this->user_service->getFullName($new_comment->user),
        'comment'     => $new_comment->comment,
      ]
    );
    
    $new_comment->human_time = $new_comment->created_at->diffForHumans();
    return $new_comment;
  }
  
  /**
   * @param int $user_course_id
   * @return object
  */
  public function getUserCourseProgress(int $user_course_id): object
  {
    return UserCourseLesson::where('user_course_id', $user_course_id)
                            ->orderBy('id', 'desc')
                            ->get();
  }
  
  /**
   * @param int $course_id
   * @param int $user_id
   * @return UserCourse|null
  */
  public function getUserCourseByCourseAndUser(int $course_id, int $user_id): ?UserCourse
  {
    return UserCourse::where('course_id', $course_id)
                    ->where('user_id', $user_id)
                    ->first();
  }
  
  /**
   * @param object $data
   * @param int $created_by
   * @return UserCourse
  */
  public function createByAdmin(object $data, int $created_by): ?UserCourse
  {
    if($this->isUserCourseActive($data->user_id, $data->course_id)) {
      throw new Exception('The course is already available for this user');
    }

    $user_course              = new UserCourse;
    $user_course->course_id   = $data->course_id;
    $user_course->user_id     = $data->user_id;
    $user_course->progress    = 0;
    $user_course->end_at      = $data->end_at;
    $user_course->status      = StatusService::ACTIVE;
    $user_course->created_by  = $created_by;
    $user_course->save();

    $user   = $this->user_service->getUserByID($data->user_id);
    $course = $this->course_service->getCourse($data->course_id);

    $this->storeUserCourseSchedule($user_course);

    $mail_data = [
      'name'        => $user->details->first_name,
      'course_name' => $course->name,
      'end_at'      => $user_course->end_at,
      'course_id'   => $data->course_id
    ];

    $this->mail_service->delay()->send($user->email, AddCourseToUserMail::class, $mail_data);
    return $user_course;
  }

  /**
   * @param int $user_id
   * @param int $content_id
   * @return bool
  */
  public function isUserAssignedToCourse(int $user_id, int $content_id): bool
  {
    return UserCourse::where('user_id', $user_id)
                     ->where('content', $content_id)
                     ->where('status', StatusService::ACTIVE)
                     ->exists();
  }

  /**
   * @param int $user_id
   * @param int $content_id
   * @return UserCourse|null
  */
  public function assignCourseToUser(int $user_id, int $content_id): ?UserCourse
  {
    try {
      if($this->isUserHasCourse($user_id, $content_id)) {
        $this->log_service->warning('User already has an active course', ['user_id' => $user_id, 'content_id' => $content_id]);
        throw new Exception('User already has an active course');
      }

      $user_course              = new UserCourse;
      $user_course->user_id     = $user_id;
      $user_course->course_id   = $content_id;
      $user_course->progress    = 0;
      $user_course->end_at      = Carbon::now()->addMonths(self::DEFAULT_USER_COURSE_PERIOD);
      $user_course->status      = StatusService::ACTIVE;
      $user_course->created_by  = $user_id;
      $user_course->save();

      $user   = $this->user_service->getUserByID($user_course->user_id);
      $course = $this->course_service->getCourseById($content_id);
      
      $this->storeUserCourseSchedule($user_course);
      $mail_data = [
        'name'        => $user->details->first_name,
        'course_name' => $course->name,
        'end_at'      => $user_course->end_at,
        'course_id'   => $content_id
      ];

      $this->mail_service->delay()->send($user->email, AddCourseToUserMail::class, $mail_data);
      $this->log_service->info('User has been assigned to course', ['user_id' => $user_id, 'content_id' => $content_id]);

      return $user_course;
    } catch(Exception $ex) {
      $this->log_service->error($ex);
      return null;
    }
  }
    
  /**
   * @param object $user_course
   * @param int $updated_by
   * @return UserCourse
  */
  public function update(object $user_course, int $updated_by): ?UserCourse
  {
    if(!$user_course = UserCourse::find($user_course->id)) {
      throw new Exception('Video not found');
    }

    $user_course->course_id   = $user_course->course_id;
    $user_course->user_id     = $user_course->user_id;
    $user_course->progress    = $user_course->progress;
    $user_course->end_at      = $user_course->end_at;
    $user_course->status      = StatusService::ACTIVE;
    $user_course->save();

    $this->log_service->info('User course has been updated', $user_course);

    return $user_course;
  }
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $term_id) {
      $this->delete($term_id, $deleted_by);
    }
  } 
  
  /**
   * @param int $user_course_id
   * @param int $deleted_by
   * @return void
  */
  public function delete(int $user_course_id, int $deleted_by)
  {
    try {
      if(!$user_course = UserCourse::find($user_course_id)) {
        $this->log_service->warning('User course was not found', ['user_course_id' => $user_course_id]);
        throw new Exception('User Course not found');
      }
  
      $user_course->delete();
      $this->log_service->info('User course has been deleted', ['user_course_id' => $user_course_id]);

      $this->deleteUserCourseSchedulesByUserCourseId($user_course->id);

    } catch(Exception $ex) {
      $this->log_service->error($ex);
    }
  }
  
  /**
   * @param int $user_id
   * @param int $course_id
   * @return bool
  */
  public function isUserCourseActive(int $user_id, int $course_id): bool
  {
    return UserCourse::where('user_id', $user_id)
                     ->where('course_id', $course_id)
                     ->where('status', StatusService::ACTIVE)
                     ->exists();
  }
  
  /**
   * @param int $user_id
   * @param int $course_id
   * @return bool
  */
  public function isUserHasCourse(int $user_id, int $course_id): bool
  {
    return UserCourse::where('user_id', $user_id)
                     ->where('course_id', $course_id)
                     ->where('status', StatusService::ACTIVE)
                     ->exists();
  }
  
  public function disableExpiredCourses()
  {
    UserCourse::where('status', StatusService::ACTIVE)->chunk(200, function ($user_courses) {
      foreach ($user_courses as $user_course) {
        try {
          if($this->isCourseExpired($user_course)) {
            $this->disableCourse($user_course->id);
          }
        } catch (Exception $ex) {
          $this->log_service->error($ex);
        }
      }
    });
  }
   
  /**
   * @param int $user_course_id
   * @return void
  */
  private function disableCourse(int $user_course_id)
  {
    if(!$user_course = UserCourse::find($user_course_id)) {
      $this->log_service->warning('User course was not found', ['user_course_id' => $user_course_id]);
      throw new Exception('User Course not found');
    }

    event(new UserCourseDisabledEvent($user_course));

    $user_course->status = StatusService::INACTIVE;
    $user_course->save();

    $this->log_service->info('User course has been disabled', ['user_course_id' => $user_course_id]);
  }
  
  /**
   * @param int $user_course_id
   * @return void
  */
  private function deleteUserCourseSchedulesByUserCourseId(int $user_course_id)
  {
    $user_course_schedules = UserCourseSchedule::where('user_course_id', $user_course_id)->select('id')->first();
    if(!$user_course_schedules) {
      return;
    } 
    
    $user_course_schedules->delete();
    UserCourseScheduleLesson::where('user_course_schedule_id', $user_course_schedules->id)->delete();
  }

  /**
   * @param UserCourse $user_course
   * @return bool
  */
  private function isCourseExpired(UserCourse $user_course): bool
  {
    $end_date = new Carbon($user_course->end_at);
    return $end_date->isPast();
  }

  private function storeUserCourseSchedule(UserCourse $user_course)
  {
    $course_schedule = $this->course_service->getCourseSchedule($user_course->course_id);
    if(!$course_schedule) {
      $this->log_service->warning(
        'Unable to set schedule for user to course schedule not found',
        ['user_id' => $user_course->user_id, 'course_id' => $user_course->course_id]
      );

      return;
    }

    UserCourseSchedule::create([
      'user_course_id'      => $user_course->id,
      'course_schedule_id'  => $course_schedule->id,
      'start_date'          => now(),
      'created_at'          => now()
    ]);
  }
}