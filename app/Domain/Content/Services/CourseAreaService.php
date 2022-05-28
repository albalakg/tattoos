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
   * @var CourseLessonService|null
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
   * @param int $trainer_id
   * @return bool
  */
  public function isTrainerInUsed(int $trainer_id): bool
  {
    return CourseArea::where('trainer_id', $trainer_id)->exists();
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
   * @return Collection
  */
  public function getAll(): Collection
  {
    return $this->baseQueryBuilder()
              ->select(
                'course_areas.id',
                'course_areas.name',
                'course_areas.trainer_id',
                'course_areas.description',
                'course_areas.course_id',
                'course_areas.status',
                'course_areas.created_at',
                'course_areas.image',
                'course_areas.trailer',
                'courses.name AS course_name',
                'course_categories.name AS course_category_name',
              )
              ->orderBy('course_areas.id', 'desc')
              ->get();
  }
          
  /**
   * Fully deletes all of the content
   *
   * @return void
  */
  public function truncate()
  {
    $course_areas_ids = CourseArea::withTrashed()->pluck('id');
    foreach($course_areas_ids AS $course_area_id) {
      $this->forceDelete($course_area_id, 0);
    }
  }

  /**
   * @param array $data
   * @param int $created_by
   * @return CourseArea|null 
  */
  public function create(array $data, int $created_by): ?CourseArea
  {
    $course_area               = new CourseArea;
    $course_area->course_id    = $data['course_id'];
    $course_area->trainer_id   = $data['trainer_id'];
    $course_area->name         = $data['name'];
    $course_area->description  = $data['description'];
    $course_area->view_order   = 0;
    $course_area->status       = StatusService::PENDING;
    $course_area->image        = FileService::create($data['image'], self::FILES_PATH);
    $course_area->trailer      = FileService::create($data['trailer'], self::FILES_PATH);
    $course_area->created_by   = $created_by;
    $course_area->save();

    $this->log_service->info('Course area ' . $course_area->id . ' has been created: ' . json_encode($course_area));

    try {
      if(!empty($data['lessons'])) {
        $result = $this->assignLessons($course_area, $data['lessons']);
        if(!$result) {
          throw new Exception('Failed to create Course Area, failed to find the assigned lessons');
        }
      }
    } catch(Exception $ex) {
      $this->forceDelete($course_area->id, $created_by);
      throw new Exception($ex->getMessage());
    }

    $course_area->load('category');
    $course_area->load('trainer');
    return $course_area;
  }

  /**
   * @param array $data
   * @param int $updated_by
   * @return CourseArea|null
  */
  public function update(array $data, int $updated_by): ?CourseArea
  {
    if(!$course_area = CourseArea::find($data['id'])) {
      throw new Exception('Course Area not found');
    };

    $course_area->course_id    = $data['course_id'];
    $course_area->name         = $data['name'];
    $course_area->trainer_id   = $data['trainer_id'];
    $course_area->description  = $data['description'];
    $course_area->view_order   = 0;
    $course_area->status       = $data['status'];
    
    if(!empty($data['image'])) {
      FileService::delete($data['image']);
      $course_area->image      = FileService::create($data['image'], self::FILES_PATH);
    }

    if(!empty($data['trailer'])) {
      FileService::delete($data['trailer']);
      $course_area->trailer    = FileService::create($data['trailer'], self::FILES_PATH);
    }

    if(!empty($data['lessons'])) {
      $this->assignLessons($course_area, $data['lessons']);
    }

    if(!empty($data['deleted_lessons'])) {
      $this->unAssignLessons($data['deleted_lessons']);
    }
    
    $course_area->save();
    $this->log_service->info('Course area ' . $course_area->id . ' has been updated: ' . json_encode($course_area));
    return $course_area;
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
   * Checks the latest view order and returns the next one
   *
   * @return int
  */
  public function getNextViewOrder(): int
  {
    $last_view_order = CourseArea::orderBy('view_order', 'desc')->value('view_order');
    return $last_view_order ? $last_view_order++ : 1; 
  }

  /**
   * Soft delete the item 
   * @param int $course_area_id
   * @param int $deleted_by
   * @return bool
  */
  public function delete(int $course_area_id, int $deleted_by): bool
  {
    if(!$course_area = $this->canDelete($course_area_id)) {
      return false;
    }
    
    $result = $course_area->delete();
    $this->log_service->info('Course area ' . $course_area_id . ' has been deleted');
    return $result;
  }
  
  /**
   * @param int $course_area_id
   * @param int $deleted_by
   * @return bool
  */
  public function forceDelete(int $course_area_id, int $deleted_by): bool
  {
    if(!$course_area = $this->canDelete($course_area_id)) {
      return false;
    }

    FileService::delete($course_area->image);
    FileService::delete($course_area->trailer);

    $result = $course_area->forceDelete();
    $this->log_service->info('Course area ' . $course_area_id . ' has been force deleted');
    return $result;
  }
  
  /**
   * @param int $course_area_id
   * @return CourseArea
  */
  private function canDelete(int $course_area_id): CourseArea
  {
    if(!$course_area = CourseArea::find($course_area_id)) {
      throw new Exception('Course Area not found');
    }

    if($this->isCourseAreaInUsed($course_area_id)) {
      $this->error_data = $this->course_lesson_service->getLessonsOfCourseArea($course_area_id);
      throw new Exception('Cannot delete Course Area that is being used');
    }

    return $course_area;
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
  
  /**
   * @param CourseArea $course_area
   * @param array $lessons
   * @return bool
  */
  private function assignLessons(CourseArea $course_area, array $lessons): bool
  {
    if(count($lessons)) {
      return $this->course_lesson_service->assignCourseArea($lessons, $course_area->id, $course_area->course_id);
    }

    return false;
  }
  
  /**
   * @param array $lessons
   * @return bool
  */
  private function unAssignLessons(array $lessons): bool
  {
    if(count($lessons)) {
      return $this->course_lesson_service->unAssignLessons($lessons);
    }

    return false;
  }
}