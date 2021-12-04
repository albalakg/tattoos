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
use App\Domain\Users\Models\UserCourseSubmission;
use App\Domain\Users\Models\UserCourseSubmissionComment;

class UserCourseService
{
  /**
   * @var LogService
  */
  private $log_service;

  /**
   * @var CourseService|null
  */
  private $course_service;

  /**
   * @var UserService|null
  */
  private $user_service;

  const DEFAULT_USER_COURSE_PERIOD = 12; // in months
  
  /**
   * Contain the error data
   *
   * @var mixed
  */
  public $error_data;

  public function __construct(CourseService $course_service = null, UserService $user_service = null)
  {
    $this->course_service = $course_service;
    $this->user_service = $user_service;
    $this->log_service = new LogService('userCourses');
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
                'price',
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
   *
   * @param int $user_course_id
   * @return void
  */
  public function getUserCourseProgress(int $user_course_id): object
  {
    return UserCourseLesson::where('user_course_id', $user_course_id)
                            ->orderBy('id', 'desc')
                            ->get();
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

    $user = $this->user_service->getUser($data->user_id);
    $course = $this->course_service->getCourse($data->course_id);

    $mail_service = new MailService;
    $mail_service->delay()->send(
      $user->email,
      AddCourseToUserMail::class,
      [
        'user_name'   => $user->fullName,
        'course'      => $course->name,
        'price'       => $user_course->price,
        'end_at'      => $course->end_at,
      ]
    );

    return $user_course;
  }

  /**
   * @param object $data
   * @return UserCourse|null
  */
  public function assignCourseToUser(object $data): ?UserCourse
  {
    try {
      $user_course              = new UserCourse;
      $user_course->user_id     = $data->user_id;
      $user_course->course_id   = $data->content_id;
      $user_course->progress    = 0;
      $user_course->end_at      = Carbon::now()->addMonths(self::DEFAULT_USER_COURSE_PERIOD);
      $user_course->status      = StatusService::ACTIVE;
      $user_course->created_by  = $data->user_id;
      $user_course->save();

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
    $user_course->price       = $user_course->price;
    $user_course->progress    = $user_course->progress;
    $user_course->end_at      = $user_course->end_at;
    $user_course->status      = StatusService::ACTIVE;
    $user_course->save();

    return $user_course;
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
        throw new Exception('User Course not found');
      }
  
      $user_course->delete();
      
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
      throw new Exception('User Course not found');
    }

    event(new UserCourseDisabledEvent($user_course));

    $user_course->status = StatusService::INACTIVE;
    $user_course->save();
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
}