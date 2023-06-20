<?php

namespace App\Domain\Users\Services;

use App\Domain\Helpers\LogService;
use App\Domain\Helpers\MailService;
use App\Domain\Helpers\StatusService;
use App\Domain\Users\Models\UserCourse;
use App\Mail\User\CourseHasBeenExpiredMail;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Content\Services\ContentService;

class DisableExpiredUserCoursesService
{

  private LogService $log_service;
  
  private Collection $expired_courses;
  
  private MailService $mail_service;
  
  private ContentService $content_service;
  
  public function __construct(ContentService $content_service, MailService $mail_service)
  {
    $this->log_service      = new LogService('users');
    $this->mail_service     = $mail_service;
    $this->content_service  = $content_service;
  }
  
  /**
   * disables all of the courses for the users that have been expired
   *
   * @return void
  */
  public function handler()
  {
    $this->findAllExpiredCourses();

    if(!count($this->expired_courses)) {
      return $this->log_service->info('Stopped since no expired courses were found');
    }

    $this->getCoursesName();
    $this->sendEmailToUsers();
    $this->disableCourses();
  }

  private function findAllExpiredCourses()
  {
    $this->expired_courses = UserCourse::join('user_details', 'user_details.user_id', 'user_courses.user_id')
                                      ->join('users', 'users.id', 'user_courses.user_id')
                                      ->where('user_courses.status', StatusService::ACTIVE)
                                      ->where('user_courses.end_at', '<', now()->startOfDay())
                                      ->select('user_courses.id', 'users.email', 'course_id', 'user_courses.user_id', 'progress', 'end_at', 'user_details.first_name', 'user_details.last_name')
                                      ->get();

    $this->log_service->info('Found expired courses', ['total' => count($this->expired_courses)]);
  }

  private function getCoursesName()
  {
    $courses_id = $this->expired_courses->pluck('course_id')->toArray();
    $courses = $this->content_service->getCoursesByIds($courses_id);
    
    $this->expired_courses = $this->expired_courses->map(function($expired_course) use($courses) {
      $expired_course->course_name = $courses->where('id', $expired_course->course_id)->first()->name;
      return $expired_course;
    });

    $this->log_service->info('Fetched the courses name');
  }

  private function sendEmailToUsers()
  {
    foreach($this->expired_courses AS $expired_course) {
      $expired_course->expired_at = now($expired_course->end)->toDateString();
      $this->mail_service->send($expired_course->email, CourseHasBeenExpiredMail::class, $expired_course);
    }

    $this->log_service->info('Sent emails to the users');
  }

  private function disableCourses()
  {
    $expired_courses_id = $this->expired_courses->pluck('id')->toArray();
    UserCourse::whereIn('id', $expired_courses_id)->update([
      'status' => StatusService::INACTIVE
    ]);

    $this->log_service->info('Disabled the following courses id', ['ids' => $expired_courses_id]);
  }
}