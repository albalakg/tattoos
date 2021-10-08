<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Content\Models\Course;
use App\Domain\Helpers\StatusService;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Interfaces\IContentService;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Content\Services\CourseAreaService;

class CourseService implements IContentService
{
  const FILES_PATH = 'content/courses';

  /**
   * @var LogService
  */
  private $log_service;

  /**
   * @var CourseAreaService|null
  */
  private $course_area_service;
  
  /**
   * Contain the error data
   *
   * @var mixed
  */
  public $error_data;
  
  public function __construct(CourseAreaService $course_area_service = null)
  {
    $this->course_area_service = $course_area_service;
    $this->log_service = new LogService('courses');
  }
  
  /**
   * @param int $course_id
   * @return Course
  */
  public function getCourse(int $course_id): ?Course
  {
    return Course::find($course_id);
  } 

  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return  $this->baseQueryBuilder()
              ->orderBy('courses.created_at', 'desc')
              ->get();
  }
  
  /**
   * @param int $category_id
   * @return bool
  */
  public function isCourseCategoryInUsed(int $category_id): bool
  {
    return Course::where('category_id', $category_id)->exists();
  }
  
  /**
   * Get courses by category id
   *
   * @param int $category_id
   * @return Collection
  */
  public function getCoursesOfCategory(int $category_id): Collection
  {
    return Course::where('category_id', $category_id)
                ->select('id', 'name', 'status')
                ->get();
  }
    
  /**
   * @param object $course_data
   * @param int $created_by
   * @return Course|null
  */
  public function create(object $course_data, int $created_by): ?Course
  {
    $course               = new Course;
    $course->category_id  = $course_data->category_id;
    $course->name         = $course_data->name;
    $course->description  = $course_data->description;
    $course->price        = $course_data->price;
    $course->discount     = $course_data->discount;
    $course->view_order   = 0;
    $course->status       = StatusService::PENDING;
    $course->image        = FileService::create($course_data->image, self::FILES_PATH);
    $course->trailer      = FileService::create($course_data->trailer, self::FILES_PATH);
    $course->created_by   = $created_by;
    $course->save();

    return $course;
  }

  /**
   * @param object $course_data
   * @param int $updated_by
   * @return Course|null
  */
  public function update(object $course_data, int $updated_by): ?Course
  {
    if(!$course = Course::find($course_data->id)) {
      throw new Exception('Course not found');
    };

    $course->category_id  = $course_data->category_id;
    $course->name         = $course_data->name;
    $course->description  = $course_data->description;
    $course->price        = $course_data->price;
    $course->discount     = $course_data->discount;
    $course->view_order   = 0;
    $course->status       = $course_data->status;
    
    if(!empty($course_data->image)) {
      FileService::delete($course->image);
      $course->image      = FileService::create($course_data->image, self::FILES_PATH);
    }
    
    if(!empty($course_data->trailer)) {
      FileService::delete($course->trailer);
      $course->trailer    = FileService::create($course_data->trailer, self::FILES_PATH);
    }
    
    $course->save();
    
    return $this->baseQueryBuilder()
            ->where('courses.id', $course->id)
            ->first();
  }
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $course_id) {
      $this->delete($course_id, $deleted_by);
    }
  } 
  
  /**
   * @param int $course_id
   * @param int $deleted_by
   * @return void
  */
  public function delete(int $course_id, int $deleted_by)
  {
    if(!$course = Course::find($course_id)) {
      throw new Exception('Course not found');
    }

    if($this->isCourseInUsed($course_id)) {
      $this->error_data = $this->course_area_service->getCourseAreasOfCourse($course_id);
      throw new Exception('Cannot delete Course that is being used');
    }

    FileService::delete($course->image);
    FileService::delete($course->trailer);

    $course->delete();
  }
   
  /**
   * @param int $course_id
   * @return bool
  */
  private function isCourseInUsed(int $course_id): bool
  {
    return $this->course_area_service->isCourseInUsed($course_id);
  }

  /**
   * Build base query
   *
   * @return Builder
  */   
  private function baseQueryBuilder(): Builder
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
          ->withCount('lessons', 'areas');
  }
}