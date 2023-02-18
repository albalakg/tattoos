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
use App\Domain\Content\Models\CourseSchedule;
use App\Domain\Content\Services\CourseAreaService;
use App\Domain\Content\Models\CourseScheduleLesson;

class CourseService implements IContentService
{
  const FILES_PATH = 'content/courses';

  private Course|null $course;
  
  private LogService $log_service;

  private CourseAreaService|null $course_area_service;
  
  public function __construct(CourseAreaService $course_area_service = null)
  {
    $this->course_area_service = $course_area_service;
    $this->log_service = new LogService('courses');
  }
  
  /**
   * @param int $course_id
   * @return Course|null
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
              ->with('recommendations')
              ->orderBy('courses.id', 'desc')
              ->get();
  }
  
  /**
   * @return Collection
  */
  public function getCoursesByIds(): Collection
  {
    return  Course::select(
                    'courses.id',
                    'courses.name',
                    'courses.image',
                    'courses.description',
                  )
                  ->orderBy('courses.id', 'desc')
                  ->get();
  }
  
  /**
   * @return Collection
  */
  public function getGuestActiveCourses(): Collection
  {
    return  Course::with('guestActiveAreasWithActiveLessons', 'category', 'details', 'recommendations')
                  ->where('status', StatusService::ACTIVE)
                  ->select('id', 'name', 'category_id', 'status', 'image', 'trailer', 'description', 'view_order', 'price')
                  ->get();
  }
  
  /**
   * @param int $course_id
   * @return Course
  */
  public function getGuestCourseById(int $course_id): Course
  {
    return  Course::where('id', $course_id)
                  ->with('guestActiveAreasWithActiveLessons', 'category', 'details')
                  ->select('id', 'name', 'category_id', 'status', 'image', 'trailer', 'description', 'view_order')
                  ->first();
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
   * @param int $course_id
   * @return bool
  */
  public function isCourseExistsById(int $course_id): bool
  {
    return Course::where('id', $course_id)->exists();
  }

  /**
   * @param array $courses_ids
   * @return Collection
  */
  public function getCoursesFullContent(array $courses_ids): Collection
  {
    return Course::whereIn('id', $courses_ids)
                 ->with('activeAreasWithActiveLessons', 'category', 'details')
                 ->select('id', 'name', 'category_id', 'status', 'image', 'trailer', 'description', 'view_order')
                 ->orderBy('view_order')
                 ->get();
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
   * Fully deletes all of the content
   *
   * @return void
  */
  public function truncate()
  {
    $courses_ids = Course::withTrashed()->pluck('id');
    foreach($courses_ids AS $course_id) {
      $this->forceDelete($course_id, 0);
    }
    Course::truncate();
  }
    
  /**
   * @param array $data
   * @param int $created_by
   * @return Course|null
  */
  public function create(array $data, int $created_by): ?Course
  {
    $course               = new Course;
    $course->category_id  = $data['category_id'];
    $course->name         = $data['name'];
    $course->description  = $data['description'];
    $course->price        = $data['price'];
    $course->discount     = $data['discount'];
    $course->view_order   = $data['view_order'] ?? 0;
    $course->status       = StatusService::PENDING;
    $course->image        = FileService::create($data['image'], self::FILES_PATH);
    $course->trailer      = FileService::create($data['trailer'], self::FILES_PATH);
    $course->created_by   = $created_by;
    $course->status       = $data['status'] ?? StatusService::PENDING;
    $course->save();

    $this->log_service->info('Course ' . $course->id . ' has been created: ' . json_encode($course));

    $course->category_name = $course->category->name;
    return $course;
  }

  /**
   * @param array $data
   * @param int $updated_by
   * @return Course|null
  */
  public function update(array $data, int $updated_by): ?Course
  {
    if(!$course = Course::find($data['id'])) {
      throw new Exception('Course not found');
    };

    $course->category_id  = $data['category_id'];
    $course->name         = $data['name'];
    $course->description  = $data['description'];
    $course->price        = $data['price'];
    $course->discount     = $data['discount'];
    $course->status       = $data['status'];
    
    if(!empty($data['image'])) {
      FileService::delete($course->image);
      $course->image      = FileService::create($data['image'], self::FILES_PATH);
    }
    
    if(!empty($data['trailer'])) {
      FileService::delete($course->trailer);
      $course->trailer    = FileService::create($data['trailer'], self::FILES_PATH);
    }
    
    $this->log_service->info('Course ' . $course->id . ' has been updated: ' . json_encode($course));

    $course->save();
    
    return $this->baseQueryBuilder()
            ->where('courses.id', $course->id)
            ->first();
  }
  
  /**
   * @param int $course_id
   * @param array $lessons
   * @param int $created_by
   * @return void
  */
  public function createSchedule(int $course_id, array $lessons, int $created_by)
  {
    $current_course_schedule  = $this->getCurrentCourseSchedule($course_id);
    $new_course_schedule      = CourseSchedule::create([
      'course_id'   => $course_id,
      'version'     => $current_course_schedule ? $current_course_schedule->version + 1 : 1,
      'created_at'  => now(),
      'created_by'  => $created_by,
    ]);

    $this->createScheduleLessons($new_course_schedule, $lessons, $created_by);

    if($current_course_schedule) {
      $this->deleteCourseScheduleLessons($current_course_schedule->id);
      $this->deleteCourseSchedule($current_course_schedule->id);
    }
  } 
    
  /**
   * @param int $course_id
   * @return null|CourseSchedule
  */
  public function getCurrentCourseSchedule(int $course_id): ?CourseSchedule
  {
    return CourseSchedule::where('course_id', $course_id)->first();
  }

  /**
   * @param array $ids
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
   * Soft delete the item 
   * @param int $course_id
   * @param int $deleted_by
   * @return bool
  */
  public function delete(int $course_id, int $deleted_by): bool
  {
    $this->validateIfCanDelete($course_id);

    $result = $this->course->delete();
    $this->log_service->info('Course ' . $course_id . ' has been deleted');
    return $result;
  }
  
  /**
   * @param int $course_id
   * @param int $deleted_by
   * @return bool
  */
  public function forceDelete(int $course_id, int $deleted_by): bool
  {
    $this->validateIfCanDelete($course_id);
    
    FileService::delete($this->course->image);
    FileService::delete($this->course->trailer);

    $result = $this->course->forceDelete();
    $this->log_service->info('Course ' . $course_id . ' has been forced deleted');
    return $result;
  }
  
  /**
   * Checks the latest view order and returns the next one
   *
   * @return int
  */
  public function getNextViewOrder(): int
  {
    $last_view_order = Course::orderBy('view_order', 'desc')->value('view_order');
    return $last_view_order ? $last_view_order++ : 1; 
  }
   
  /**
   * Throws an error if failed the validation and cannot delete
   * If it can be deleted, stores the content in the class state
   * @param int $course_id
   * @return void
  */
  private function validateIfCanDelete(int $course_id)
  {
    if(!$course = Course::find($course_id)) {
      throw new Exception('Course not found');
    }

    if($this->isCourseInUsed($course_id)) {
      throw new Exception('Cannot delete Course that is being used');
    }

    $this->course = $course;
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
   * @param int $course_schedule_id
   * @return void
  */
  private function deleteCourseSchedule(int $course_schedule_id)
  {
    return CourseSchedule::where('id', $course_schedule_id)->delete();
  }
    
  /**
   * @param int $course_schedule_id
   * @return void
  */
  private function deleteCourseScheduleLessons(int $course_schedule_id)
  {
    return CourseScheduleLesson::where('course_schedule_id', $course_schedule_id)->delete();
  }

  /**
   * @param CourseSchedule $new_course_schedule
   * @param array $lessons
   * @param int $created_by
   * @return void
  */
  private function createScheduleLessons(CourseSchedule $new_course_schedule, array $lessons, int $created_by)
  {
    CourseScheduleLesson::insert(array_map(function($lesson) use($new_course_schedule, $created_by) {
      return [
        'course_schedule_id'  => $new_course_schedule->id,
        'course_id'           => $new_course_schedule->course_id,
        'course_lesson_id'    => $lesson['id'],
        'date'                => $lesson['date'] ?? now(),
        'created_at'          => now(),
        'created_by'          => $created_by,
      ];
    }, $lessons));
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