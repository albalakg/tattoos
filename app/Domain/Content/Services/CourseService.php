<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Content\Models\Course;
use App\Domain\Helpers\StatusService;
use App\Domain\Interfaces\IContentService;

class CourseService implements IContentService
{
  const FILES_PATH = 'content/courses';

  /**
   * @var LogService
  */
  private $log_service;
  
  public function __construct()
  {
    $this->log_service = new LogService('courses');
  }
  
    /**
   * @return object
  */
  public function getAll(): object
  {
    return Course::join('course_categories', 'course_categories.id', 'courses.category_id')
              ->select(
                'courses.id',
                'courses.name',
                'courses.category_id',
                'courses.status',
                'courses.image',
                'courses.trailer',
                'courses.price',
                'courses.description',
                'courses.discount',
                'courses.created_at',
                'course_categories.name AS category_name',
              )
              ->orderBy('courses.created_at', 'desc')
              ->withCount('lessons')
              ->simplePaginate(1000);
  }
    
  /**
   * @param object $courseData
   * @param int $created_by
   * @return Course|null
  */
  public function create(object $courseData, int $created_by): ?Course
  {
    $course               = new Course;
    $course->category_id  = $courseData->category_id;
    $course->name         = $courseData->name;
    $course->description  = $courseData->description;
    $course->price        = $courseData->price;
    $course->discount     = $courseData->discount;
    $course->view_order   = 0;
    $course->status       = StatusService::PENDING;
    $course->image        = FileService::create($courseData->image, self::FILES_PATH);
    $course->trailer      = FileService::create($courseData->trailer, self::FILES_PATH);
    $course->created_by   = $created_by;
    $course->save();

    return $course;
  }

  /**
   * @param object $courseData
   * @param int $updated_by
   * @return Course|null
  */
  public function update(object $courseData, int $updated_by): ?Course
  {
    if(!$course = Course::find($courseData->id)) {
      throw new Exception('Course not found');
    };

    $course->category_id  = $courseData->category_id;
    $course->name         = $courseData->name;
    $course->description  = $courseData->description;
    $course->price        = $courseData->price;
    $course->discount     = $courseData->discount;
    $course->view_order   = 0;
    $course->status       = $courseData->status;
    $course->updated_by   = $updated_by;
    
    if(!empty($course->image)) {
      $course->image        = FileService::create($courseData->image, self::FILES_PATH);
    }

    if(!empty($course->trailer)) {
      $course->trailer        = FileService::create($courseData->trailer, self::FILES_PATH);
    }
    
    $course->save();
    return $course;
  }
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $course_id) {
      if($error = $this->delete($course_id, $deleted_by)) {
        return $error;
      }
    }
  } 
  
  /**
   * @param int $course_id
   * @param int $deleted_by
   * @return void
  */
  public function delete(int $course_id, int $deleted_by)
  {
    try {
      
    } catch(Exception $ex) {
    }
  }
}