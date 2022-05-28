<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Helpers\StatusService;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Interfaces\IContentService;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Content\Models\CourseCategory;

class CourseCategoryService implements IContentService
{
  const FILES_PATH = 'content/course-categories';

  /**
   * @var LogService
  */
  private $log_service;
  
  /**
   * @var CourseService|null
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
   * @return Collection
  */
  public function getAll(): Collection
  {
    return  $this->baseQueryBuilder()
              ->orderBy('id', 'desc')
              ->get();
  }
  
  /**
   * @return Collection
  */
  public function getActive(): Collection
  {
    return $this->getByStatus(StatusService::ACTIVE);
  }
  
  /**
   * @return null|CourseCategory
  */
  public function getRandomCategory(): ?CourseCategory
  {
    return CourseCategory::inRandomOrder()->first();
  }
  
  /**
   * @param int $status
   * @return Collection
  */
  public function getByStatus(int $status): Collection
  {
    return $this->baseQueryBuilder()
              ->where('status', $status)
              ->orderBy('id', 'desc')
              ->get();
  }
      
  /**
   * Fully deletes all of the content
   *
   * @return void
  */
  public function truncate()
  {
    $categories_ids = CourseCategory::withTrashed()->pluck('id');
    foreach($categories_ids AS $category_id) {
      $this->forceDelete($category_id, 0);
    }
    CourseCategory::truncate();
  }

  /**
   * @param array $data
   * @param int $created_by
   * @return CourseCategory|null
  */
  public function create(array $data, int $created_by): ?CourseCategory
  {
    $course_category               = new CourseCategory;
    $course_category->name         = $data['name'];
    $course_category->description  = $data['description'];
    $course_category->image        = FileService::create($data['image'], self::FILES_PATH);
    $course_category->status       = StatusService::PENDING;
    $course_category->created_by   = $created_by;
    $course_category->status       = $data['status'] ?? StatusService::PENDING;
    $course_category->save();

    $this->log_service->info('Course Category ' . $course_category->id . ' has been created: ' . json_encode($course_category));
    return $course_category;
  }
    
  /**
   * @param array $data
   * @param int $updated_by
   * @return CourseCategory|null
  */
  public function update(array $data, int $updated_by): ?CourseCategory
  {
    if(!$course_category = CourseCategory::find($data['id'])) {
      throw new Exception('Course Category not found');
    };

    $course_category->name         = $data['name'];
    $course_category->description  = $data['description'];
    if(!empty($data['image'])) {
      FileService::delete($course_category->image);
      $course_category->image      = FileService::create($data['image'], self::FILES_PATH);
    }
    $course_category->status       = StatusService::PENDING;
    $course_category->save();

    $this->log_service->info('Course Category ' . $course_category->id . ' has been updated: ' . json_encode($course_category));
    return $course_category;
  }
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $course_category_id) {
      if($error = $this->delete($course_category_id, $deleted_by)) {
        return $error;
      }
    }
  } 
  
  /**
   * Soft delete the item
   * 
   * @param int $course_category_id
   * @param int $deleted_by
   * @return bool
  */
  public function delete(int $course_category_id, int $deleted_by): bool
  {
    if(!$course_category = $this->canDelete($course_category_id)) {
      return false;
    }

    $result = $course_category->delete();
    $this->log_service->info('Course Category ' . $course_category_id . ' has been deleted');
    return $result;
  }
  
  /**
   * @param int $course_category_id
   * @param int $deleted_by
   * @return bool
  */
  public function forceDelete(int $course_category_id, int $deleted_by): bool
  {
    if(!$course_category = $this->canDelete($course_category_id)) {
      return false;
    }

    FileService::delete($course_category->image);
    
    $result = $course_category->forceDelete();
    $this->log_service->info('Course Category ' . $course_category_id . ' has been forced deleted');
    return $result;
  }
  
  /**
   * @param int $course_category_id
   * @return CourseCategory
  */
  private function canDelete(int $course_category_id): CourseCategory
  {
    if(!$course_category = CourseCategory::find($course_category_id)) {
      throw new Exception('Course Category not found');
    }

    if($this->isInUsed($course_category_id)) {
      $this->error_data = $this->course_service->getCoursesOfCategory($course_category_id);
      throw new Exception('Cannot delete Course Category that is being used');
    }

    return $course_category;
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
          );
  }
}