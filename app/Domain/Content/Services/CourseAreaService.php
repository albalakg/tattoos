<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Helpers\StatusService;
use App\Domain\Content\Models\CourseArea;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Interfaces\IContentService;
use Illuminate\Database\Eloquent\Collection;

class CourseAreaService implements IContentService
{
  const FILES_PATH = 'content/courses-areas';

  /**
   * @var LogService
  */
  private $log_service;
  
  /**
   * @var CourseLessonService
  */
  private $course_lesson_service;
    
  /**
   * Contain the error data
   *
   * @var mixed
  */
  public $error_data;
  
  public function __construct(CourseLessonService $course_lesson_service = null)
  {
    $this->course_lesson_service = $course_lesson_service;
    $this->log_service = new LogService('courseAreas');
  }
  
  /**
   * @param int $course_area_id
   * @return CourseArea|null
  */
  public function getById(int $course_area_id): ?CourseArea
  {
    return CourseArea::find($course_area_id);
  }

  /**
   * @param int $course_id
   * @return bool
  */
  public function isCourseInUsed(int $course_id): bool
  {
    return CourseArea::where('course_id', $course_id)->exists();
  }

  /**
   * @param int $course_id
   * @return Collection
  */
  public function getCourseAreasOfCourse(int $course_id): Collection
  {
    return CourseArea::where('course_id', $course_id)
                    ->select('id', 'name', 'status')
                    ->get();
  }
  
  /**
   * @return object
  */
  public function getAll(): object
  {
    return $this->baseQueryBuilder()
              ->select(
                'course_areas.id',
                'course_areas.name',
                'course_areas.description',
                'course_areas.course_id',
                'course_areas.status',
                'course_areas.created_at',
                'course_areas.image',
                'course_areas.trailer',
                'courses.name AS course_name',
                'course_categories.name AS course_category_name',
              )
              ->withCount('lessons')
              ->orderBy('course_areas.created_at', 'desc')
              ->simplePaginate(1000);
  }
    
  /**
   * @param object $courseAreaData
   * @param int $created_by
   * @return CourseArea|null 
  */
  public function create(object $courseAreaData, int $created_by): ?CourseArea
  {
    $courseArea               = new CourseArea;
    $courseArea->course_id    = $courseAreaData->course_id;
    $courseArea->name         = $courseAreaData->name;
    $courseArea->description  = $courseAreaData->description;
    $courseArea->view_order   = 0;
    $courseArea->status       = StatusService::PENDING;
    $courseArea->image        = FileService::create($courseAreaData->image, self::FILES_PATH);
    $courseArea->trailer      = FileService::create($courseAreaData->trailer, self::FILES_PATH);
    $courseArea->created_by   = $created_by;
    $courseArea->save();

    $courseArea->load('category');
    return $courseArea;
  }

  /**
   * @param object $courseAreaData
   * @param int $updated_by
   * @return CourseArea|null
  */
  public function update(object $courseAreaData, int $updated_by): ?CourseArea
  {
    if(!$courseArea = CourseArea::find($courseAreaData->id)) {
      throw new Exception('Course Area not found');
    };

    $courseArea->course_id    = $courseAreaData->course_id;
    $courseArea->name         = $courseAreaData->name;
    $courseArea->description  = $courseAreaData->description;
    $courseArea->view_order   = 0;
    $courseArea->status       = $courseAreaData->status;
    
    if(!empty($courseAreaData->image)) {
      FileService::delete($courseAreaData->image);
      $courseArea->image      = FileService::create($courseAreaData->image, self::FILES_PATH);
    }

    if(!empty($courseAreaData->trailer)) {
      FileService::delete($courseAreaData->trailer);
      $courseArea->trailer    = FileService::create($courseAreaData->trailer, self::FILES_PATH);
    }
    
    $courseArea->save();
    return $courseArea;
  }
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $course_area_id) {
      if($error = $this->delete($course_area_id, $deleted_by)) {
        return $error;
      }
    }
  } 
  
  /**
   * @param int $course_area_id
   * @param int $deleted_by
   * @return void
  */
  public function delete(int $course_area_id, int $deleted_by)
  {
    if(!$course_area = CourseArea::find($course_area_id)) {
      throw new Exception('Course Area not found');
    }

    if($this->isCourseAreaInUsed($course_area_id)) {
      $this->error_data = $this->course_lesson_service->getLessonsOfCourseArea($course_area_id);
      throw new Exception('Cannot delete Course Area that is being used');
    }

    FileService::delete($course_area->image);
    FileService::delete($course_area->trailer);

    $course_area->delete();
  }
  
  /**
   * @param int $course_area_id
   * @return bool
  */
  private function isCourseAreaInUsed(int $course_area_id): bool
  {
    return $this->course_lesson_service->isCourseAreaInUsed($course_area_id);
  }
  
  /**
   * Build base query
   *
   * @return Builder
  */   
  private function baseQueryBuilder(): Builder
  {
    return CourseArea::join('courses', 'courses.id', 'course_areas.course_id')
            ->join('course_categories', 'course_categories.id', 'courses.category_id');
  }
}