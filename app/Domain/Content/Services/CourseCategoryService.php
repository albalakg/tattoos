<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Helpers\StatusService;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Interfaces\IContentService;
use App\Domain\Content\Models\CourseCategory;

class CourseCategoryService implements IContentService
{
  const FILES_PATH = 'content/course-categories';

  /**
   * @var LogService
  */
  private $log_service;
  
  /**
   * @var CourseService
  */
  private $course_service;
  
  /**
   * Contain the error data
   *
   * @var mixed
  */
  public $error_data;

  public function __construct(CourseService $course_service = null)
  {
    $this->course_service = $course_service;
    $this->log_service = new LogService('courseCategories');
  }
  
    /**
   * @return object
  */
  public function getAll(): object
  {
    return  $this->baseQueryBuilder()
              ->simplePaginate(1000);
  }
    
  /**
   * @param object $data
   * @param int $created_by
   * @return CourseCategory|null
  */
  public function create(object $data, int $created_by): ?CourseCategory
  {
    $courseCategory               = new CourseCategory;
    $courseCategory->name         = $data->name;
    $courseCategory->description  = $data->description;
    $courseCategory->image        = FileService::create($data->image, self::FILES_PATH);
    $courseCategory->status       = StatusService::PENDING;
    $courseCategory->created_by   = $created_by;
    $courseCategory->save();

    return $courseCategory;
  }
    
  /**
   * @param object $data
   * @param int $updated_by
   * @return CourseCategory|null
  */
  public function update(object $data, int $updated_by): ?CourseCategory
  {
    if(!$courseCategory = CourseCategory::find($data->id)) {
      throw new Exception('Course Category not found');
    };

    $courseCategory->name         = $data->name;
    $courseCategory->description  = $data->description;
    if(!empty($data->image)) {
      FileService::delete($courseCategory->image);
      $courseCategory->image      = FileService::create($data->image, self::FILES_PATH);
    }
    $courseCategory->status       = StatusService::PENDING;
    $courseCategory->save();

    return $courseCategory;
  }
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $courseCategory_id) {
      if($error = $this->delete($courseCategory_id, $deleted_by)) {
        return $error;
      }
    }
  } 
  
  /**
   * @param int $courseCategory_id
   * @param int $deleted_by
   * @return void
  */
  public function delete(int $courseCategory_id, int $deleted_by)
  {
    if(!$course_category = CourseCategory::find($courseCategory_id)) {
      throw new Exception('Course Category not found');
    }

    if($this->isInUsed($courseCategory_id)) {
      $this->error_data = $this->course_service->getCoursesOfCategory($courseCategory_id);
      throw new Exception('Cannot delete Course that is being used');
    }

    FileService::delete($course_category->image);

    $course_category->delete();
  }
  
  /**
   * @param string $path
   * @return bool
  */
  private function deleteCourseFile(string $path): bool
  {
    return FileService::delete($path);
  }
   
  /**
   * @param int $course_id
   * @return bool
  */
  private function isInUsed(int $course_id): bool
  {
    return $this->course_service->isCourseCategoryInUsed($course_id);
  }

  /**
   * Build base query
   *
   * @return Builder
  */   
  private function baseQueryBuilder(): Builder
  {
    return CourseCategory::select(
            'id',
            'name',
            'description',
            'image',
            'status',
            'created_at',
          )
          ->withCount('courses');
  }
}