<?php

namespace App\Domain\Users\Services;

use Exception;
use App\Domain\Helpers\LogService;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Users\Models\UserCourseSchedule;
use App\Domain\Content\Services\ContentService;
use App\Domain\Users\Models\UserCourseScheduleLesson;

class UserCourseScheduleService
{
  private ?ContentService $content_service;
  private LogService $log_service;

  public function __construct(ContentService $content_service = null)
  {
    $this->content_service  = $content_service;
    $this->log_service      = new LogService('userSchedules');
  }

    
  /**
   * @param int $course_schedule_lesson_id
   * @param string $date
   * @param int $user_id
   * @return UserCourseScheduleLesson
  */
  public function scheduleLesson(int $course_schedule_lesson_id, string $date, int $user_id): UserCourseScheduleLesson
  {
    $user_course_schedule = $this->getUserCourseScheduleByUserId($user_id);
    if(!$user_course_schedule) {
      throw new Exception('User course schedule not found');
    }
    
    $course_schedule_lesson = $this->content_service->getCourseScheduleLessonById($course_schedule_lesson_id);
    if(!$course_schedule_lesson) {
      throw new Exception('Course schedule lesson not found');
    }

    UserCourseScheduleLesson::where('course_schedule_lesson_id', $course_schedule_lesson_id)
                            ->delete();

    $user_course_schedule_lesson = UserCourseScheduleLesson::create(
      [
        'course_schedule_lesson_id' => $course_schedule_lesson_id,
        'user_course_schedule_id'   => $user_course_schedule->id,
        'course_lesson_id'          => $course_schedule_lesson->course_lesson_id,
        'user_id'                   => $user_id,
        'date'                      => $date,
        'created_by'                => $user_id,
        'type_id'                   => $course_schedule_lesson->type_id
      ]
    );

    $this->log_service->info('Rescheduled user course lesson', [
        'user_course_schedule_id' => $user_course_schedule->id,
        'course_lesson_id'        => $course_schedule_lesson->course_lesson_id,
        'user_id'                 => $user_id,
        'date'                    => $date
    ]);

    return $user_course_schedule_lesson;
  } 
  
  /**
   * @param int $user_id
   * @return ?UserCourseSchedule
  */
  public function getUserCourseScheduleByUserId(int $user_id): ?UserCourseSchedule
  {
    return UserCourseSchedule::where('user_id', $user_id)->first();
  }
  
  /**
   * @param int $user_id
   * @return Collection
  */
  public function getUserCourseScheduleWithScheduleCourseByUserId(int $user_id): Collection
  {
    return UserCourseSchedule::where('user_course_schedules.user_id', $user_id)
                             ->join('course_schedules', 'course_schedules.id', 'user_course_schedules.course_schedule_id')
                             ->with('lessons')
                             ->select(
                              'user_course_schedules.id',
                              'user_course_schedules.course_schedule_id',
                              'course_schedules.course_id',
                             )
                             ->get();
  }
}