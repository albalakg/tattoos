<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Content\Models\Coupon;
use App\Domain\Content\Models\Course;
use App\Domain\Content\Models\Video;
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
  public function getCoursesByIds(array $content_ids): ?Collection
  {
    try {
      $course_service = new CourseService;
      return $course_service->getCoursesByIds($content_ids);
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

  /**
   * @param int $course_id
   * @return Collection|null
  */
  public function getLessonsDurationByCourseId(int $course_id): ?Collection
  {
    try {
      $lesson_service = new CourseLessonService;
      return $lesson_service->getLessonsDurationByCourseId($course_id);
    } catch(Exception $ex) {
      $this->log_service->error($ex);
      return null;
    }
  }
  
  /**
   * @param int $lesson_id
   * @return int|null
  */
  public function getLessonCourseId(int $lesson_id): ?int
  {
    try {
      $lesson_service = new CourseLessonService;
      return $lesson_service->getLessonCourseId($lesson_id);
    } catch(Exception $ex) {
      $this->log_service->error($ex);
      return null;
    }
  }
  
  /**
   * @param int $course_id
   * @return Course|null
  */
  public function getCourse(int $course_id): ?Course
  {
    try {
      $course_service = new CourseService;
      return $course_service->getCourse($course_id);
    } catch(Exception $ex) {
      $this->log_service->error($ex);
      return false;
    }
  }
  
  /**
   * @param string $coupon_code
   * @return Coupon|null
  */
  public function getCoupon(string $coupon_code): ?Coupon
  {
    try {
      $coupon_service = new CouponService;
      return $coupon_service->getByCode($coupon_code);
    } catch(Exception $ex) {
      $this->log_service->error($ex);
      return false;
    }
  }
  
  /**
   * @param int $lesson_id
   * @return Video|null
  */
  public function getVideoByLessonId(int $lesson_id): ?Video
  {
    try {
      $coupon_service = new CourseLessonService;
      return $coupon_service->getVideoByLessonId($lesson_id);
    } catch(Exception $ex) {
      $this->log_service->error($ex);
      return null;
    }
  }
}