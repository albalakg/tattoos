<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\StatusService;
use App\Domain\Interfaces\IContentService;
use App\Domain\Content\Models\CourseCategory;

class CourseCategoryService implements IContentService
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
    return CourseCategory::select(
                'id',
                'name',
                'description',
                'image',
                'status',
                'created_at',
              )
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
    $courseCategory               = new CourseCategory;
    $courseCategory->name         = $data->name;
    $courseCategory->description  = $data->description;
    $courseCategory->status       = StatusService::PENDING;
    $courseCategory->updated_by   = $updated_by;
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
    try {
    } catch(Exception $ex) {
    }
  }
}