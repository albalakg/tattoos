<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Content\Services\CourseLessonService;


/**
 * The content service is the SDK for all content related requests
*/
class ContentService
{
  /**
   * @var LogService
  */
  private $log_service;

  public function __construct()
  {
    $this->log_service = new LogService('content');
  }

  /**
   * @param array $courses_ids
   * @return Collection|null
  */
  public function getCoursesFullContent(array $courses_ids): ?Collection
  {
    try {
      $course_service = new CourseService;
      return $course_service->getCoursesFullContent($courses_ids);
    } catch(Exception $ex) {
      $this->log_service->error($ex);
      return null;
    }
  }

  /**
   * @param array $content_ids
   * @return Collection|null
  */
  public function getLessonsByIds(array $content_ids): ?Collection
  {
    try {
      $lesson_service = new CourseLessonService;
      return $lesson_service->getLessonsByIds($content_ids);
    } catch(Exception $ex) {
      $this->log_service->error($ex);
      return null;
    }
  }
}