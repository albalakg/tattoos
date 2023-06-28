<?php

namespace App\Domain\Users\Services;

use App\Domain\Content\Models\CourseScheduleLesson;
use Exception;
use App\Domain\Helpers\LogService;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Users\Models\UserCourseSchedule;
use App\Domain\Content\Services\ContentService;
use App\Domain\Helpers\StatusService;
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
   * @param array $data
   * @param int $user_id
   * @return UserCourseScheduleLesson
  */
  public function addTrainingSchedule(array $data, $user_id): UserCourseScheduleLesson
  {
    $user_course_schedule = $this->getUserCourseScheduleByUserId($user_id);
    if(!$user_course_schedule) {
      throw new Exception('User course schedule not found');
    }

    return UserCourseScheduleLesson::create([
      'type_id'                 => CourseScheduleLesson::TRAINING_TYPE_ID,
      'user_course_schedule_id' => $user_course_schedule->id,
      'user_id'                 => $user_id,
      'course_lesson_id'        => $data['lesson_id'],
      'date'                    => $data['date'],
      'created_by'              => $user_id,
    ]);
  }
  
  /**
   * @param int $schedule_id
   * @param string $date
   * @param int $user_id
   * @return UserCourseScheduleLesson
  */
  public function updateTrainingSchedule(int $schedule_id, string $date, $user_id): UserCourseScheduleLesson
  {
    $user_course_schedule = $this->getUserCourseScheduleByUserId($user_id);
    if(!$user_course_schedule) {
      throw new Exception('User course schedule not found');
    }

    $user_course_schedule_lesson = UserCourseScheduleLesson::where('id', $schedule_id)
                                                          ->where('user_id', $user_id)
                                                          ->first();

    if(!$user_course_schedule_lesson) {
      throw new Exception('User\'s schedule lesson was not found');
    }

    $user_course_schedule_lesson->date = $date;
    $user_course_schedule_lesson->save();

    return $user_course_schedule_lesson;
  }
  
  /**
   * @param int $schedule_id
   * @param int $user_id
   * @return void
  */
  public function deleteTrainingSchedule(int $schedule_id, $user_id)
  {
    $user_course_schedule = $this->getUserCourseScheduleByUserId($user_id);
    if(!$user_course_schedule) {
      throw new Exception('User course schedule not found');
    }

    UserCourseScheduleLesson::where('id', $schedule_id)
                            ->where('user_id', $user_id)
                            ->delete();
  }
  
  /**
   * @param int $user_id
   * @return Collection
  */
  public function getUserCourseScheduleWithScheduleCourseByUserId(int $user_id): Collection
  {
    return UserCourseSchedule::join('user_courses', 'user_courses.id', 'user_course_schedules.user_course_id')
                              ->join('course_schedules', 'course_schedules.id', 'user_course_schedules.course_schedule_id')
                              ->where('user_courses.user_id', $user_id)
                              ->with('lessons')
                              ->select(
                                'user_course_schedules.id',
                                'user_course_schedules.course_schedule_id',
                                'course_schedules.course_id',
                              )
                              ->get();
  }
  
  /**
   * @param int $user_id
   * @return ?UserCourseSchedule
  */
  private function getUserCourseScheduleByUserId(int $user_id): ?UserCourseSchedule
  {
    return UserCourseSchedule::join('user_courses', 'user_courses.id','user_course_schedules.user_course_id')
                             ->where('user_courses.user_id', $user_id)
                             ->where('user_courses.status', StatusService::ACTIVE)
                             ->select('user_course_schedules.*')
                             ->first();
  }
}