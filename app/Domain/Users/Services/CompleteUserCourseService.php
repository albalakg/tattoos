<?php

namespace App\Domain\Users\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Users\Services\UserService;
use App\Domain\Content\Services\CourseService;
use App\Domain\Users\Services\UserCourseService;
use App\Domain\Content\Services\CourseLessonService;
use App\Domain\Users\Models\UserCourse;
use App\Domain\Users\Models\UserCourseLesson;

/**
 * Completing a course for user.
 * 
 * The idea is to to fetch the course lessons and than the user current progress.
 * Eliminate the completed lessons from the total lessons of the course, prepare a single 
 * array of data (completed lessons record by the users), and than to insert it with a single query.
 * 
 * The point is to avoid duplication of completed lessons minimum queries.
 */
class CompleteUserCourseService
{
  private LogService $log_service;

  private CourseLessonService $course_lesson_service;

  private UserService $user_service;

  private CourseService $course_service;

  private UserCourseService $user_course_service;
  
  private int $course_id;
  
  private int $user_id;

  private array $lessons_id = [];

  private array $current_completed_lessons_id = [];

  private array $new_completed_lessons = [];

  private int $user_course_id;
  
  /**
   * Complete the course for the given user
   * Fill his progress till completion
   *
   * @param  mixed $course_lesson_service
   * @param  mixed $user_service
   * @return void
  */
  public function __construct(
    CourseLessonService $course_lesson_service,
    UserCourseService $user_course_service,
    CourseService $course_service,
    UserService $user_service,
  )
  {
    $this->course_lesson_service  = $course_lesson_service;
    $this->user_course_service    = $user_course_service;
    $this->course_service         = $course_service;
    $this->user_service           = $user_service;
    $this->log_service            = new LogService('CompleteUserCourseService');
  }
    
  /**
   * @param int $course_id
   * @param int $user_id
   * @return void
  */
  public function run(int $course_id, int $user_id)
  {
    $this->course_id  = $course_id;
    $this->user_id    = $user_id;

    $this->validateIfGoodToGo();

    $this->getCourseLessons();
    $this->getUserCompletedLessons();
    $this->prepareNewRecords();
    $this->storeUserProgress();
  }

  private function getCourseLessons()
  {
    $this->lessons_id = $this->course_lesson_service->getLessonsByCoursesId($this->course_id)
                                                 ->pluck('id')
                                                 ->toArray();
  }
  
  private function getUserCompletedLessons()
  {
    $this->user_course_id                 = $this->user_course_service->getUserCourseByCourseAndUser($this->course_id, $this->user_id)->id;
    $this->current_completed_lessons_id   = $this->user_course_service->getUserCourseProgress($this->user_course_id)
                                                 ->pluck('id')
                                                 ->toArray();
  }
  
  private function prepareNewRecords()
  {
    foreach($this->lessons_id AS $lesson_id) {
      if(!$this->isLessonAlreadyCompleted($lesson_id)) {
        $this->addNewCompletedLesson($lesson_id);
      }
    } 
  }
  
  private function isLessonAlreadyCompleted(int $lesson_id): bool
  {
    return in_array($lesson_id, $this->current_completed_lessons_id);
  }
  
  private function addNewCompletedLesson(int $lesson_id)
  {
    $this->new_completed_lessons = [
      'user_course_id'    => $this->user_course_id,
      'user_id'           => $this->user_id,
      'course_lesson_id'  => $lesson_id,
      'progress'          => UserCourse::DONE,
      'created_at'        => now(),
      'updated_at'        => now()
    ];
  }
  
  private function storeUserProgress()
  {
    UserCourseLesson::insert($this->new_completed_lessons);
  }
  
  /**
   * Validate that the course and the user exists.
   * Than Check that the user is assigned to the course
   *
   * @return void
  */
  private function validateIfGoodToGo()
  {
    if(!$this->user_service->isUserExistsById($this->user_id)) {
      $this->error('User not found with the value "' . $this->user_id . '"');
    }

    if(!$this->course_service->isCourseExistsById($this->course_id)) {
      $this->error('Course not found with the value "' . $this->course_id . '"');
    }

    if(!$this->user_course_service->isUserHasCourse($this->user_id, $this->course_id)) {
      $this->error('User is not assigned to the course with the values, course: "' . $this->course_id . '", and user: "' . $this->user_id . '"');
    }
  }
    
  private function error(string $error)
  {
    $this->log_service->error($error);
    throw new Exception($error);
  }
}