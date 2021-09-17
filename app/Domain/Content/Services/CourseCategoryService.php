<?php

namespace App\Domain\Content\Services;

use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Helpers\StatusService;
use App\Domain\Content\Models\CourseCategory;

class CourseCategoryService
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
  public function createCourseCategory(object $data, int $created_by): ?CourseCategory
  {
    $courseCategory               = new CourseCategory;
    $courseCategory->name         = $data->name;
    $courseCategory->description  = $data->description;
    $courseCategory->status       = StatusService::PENDING;
    $courseCategory->created_by   = $created_by;
    $courseCategory->save();

    return $courseCategory;
  }
}