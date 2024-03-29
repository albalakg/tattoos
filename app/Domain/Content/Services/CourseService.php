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
use App\Domain\Users\Models\UserCourseSchedule;
use App\Domain\Users\Services\UserCourseService;
use App\Domain\Content\Services\CourseAreaService;
use App\Domain\Content\Models\CourseScheduleLesson;

class CourseService implements IContentService
{
  const FILES_PATH = 'content/courses';

  private LogService $log_service;

  private ?Course $course;

  private ?CourseAreaService $course_area_service;

  private ?UserCourseService $user_course_service;
  
  public function __construct(
    CourseAreaService $course_area_service = null,
    UserCourseService $user_course_service = null    
  )
  {
    $this->user_course_service  = $user_course_service;
    $this->course_area_service  = $course_area_service;
    $this->log_service          = new LogService('courses');
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
              ->with('recommendations', 'schedules')
              ->orderBy('courses.id', 'desc')
              ->get();
  }
  
  /**
   * @param int $course_id
   * @return Collection
  */
  public function getCourseById(int $course_id): Collection
  {
    return  Course::select(
                    'courses.id',
                    'courses.name',
                    'courses.image',
                    'courses.description',
                  )
                  ->where('id', $course_id)
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
                  ->select('id', 'name', 'category_id', 'status', 'image', 'trailer', 'description', 'view_order', 'price', 'discount')
                  ->get();
  }
  
  /**
   * @param int $course_id
   * @return Course
  */
  public function getGuestCourseById(int $course_id): Course
  {
    return  Course::where('id', $course_id)
                  ->with('guestActiveAreasWithActiveLessons', 'category', 'details', 'recommendations')
                  ->select('id', 'name', 'category_id', 'status', 'image', 'trailer', 'description', 'view_order', 'price')
                  ->first();
  }
  
  /**
   * @return Course
  */
  public function getGuestOnlyCourse(): Course
  {
    return  Course::with('guestActiveAreasWithActiveLessons', 'category', 'details', 'recommendations')
                  ->select('id', 'name', 'category_id', 'status', 'image', 'trailer', 'description', 'view_order', 'price')
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
                 ->with('activeAreasWithActiveLessons', 'category', 'details', 'recommendations', 'schedules')
                 ->select('id', 'name', 'category_id', 'status', 'image', 'trailer', 'description', 'view_order', 'price', 'discount')
                 ->orderBy('view_order')
                 ->get();
  }
  
  /**
   * @param int $course_schedule_lesson_id
   * @return ?CourseScheduleLesson
  */
  public function getCourseScheduleLessonById(int $course_schedule_lesson_id): ?CourseScheduleLesson
  {
    return CourseScheduleLesson::find($course_schedule_lesson_id);
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
    $course->trailer      = !empty($data['trailer']) ? FileService::create($data['trailer'], self::FILES_PATH) : null;
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
    $current_course_schedule  = $this->getCourseSchedule($course_id);
    $this->deleteOldCourseSchedules($course_id);
    $new_course_schedule      = CourseSchedule::create([
      'course_id'   => $course_id,
      'version'     => $current_course_schedule ? $current_course_schedule->version + 1 : 1,
      'created_at'  => now(),
      'created_by'  => $created_by,
    ]);

    if($current_course_schedule) {
      $this->deleteCourseScheduleLessons($current_course_schedule->id);
    }

    $this->createScheduleLessons($new_course_schedule, $lessons, $created_by);
    $this->setCourseScheduleToActiveCourseMembersWithoutSchedule($new_course_schedule);
  } 
    
  /**
   * @param int $course_id
   * @return null|CourseSchedule
  */
  public function getCourseSchedule(int $course_id): ?CourseSchedule
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
   * @param CourseSchedule $course_schedule
   * @return void
  */
  private function setCourseScheduleToActiveCourseMembersWithoutSchedule(CourseSchedule $course_schedule)
  {
    $course_users         = $this->user_course_service->getActiveCourseUsersWithoutSchedule($course_schedule->course_id);
    $new_users_course_schedule = [];
    foreach($course_users AS $course_user) {
      $new_users_course_schedule[] = [
        'user_course_id'      => $course_user->id,
        'course_schedule_id'  => $course_schedule->id,
        'start_date'          => now(),
        'created_at'          => now(),
        
      ];
    }
    UserCourseSchedule::insert($new_users_course_schedule);
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

    if($this->isCourseHasContent($course_id)) {
      throw new Exception('Cannot delete Course that has content');
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
  private function isCourseHasContent(int $course_id): bool
  {
    return $this->course_area_service->isCourseInUsed($course_id);
  }

  /**
   * @param int $course_id
   * @return bool
  */
  private function isCourseInUsed(int $course_id): bool
  {
    return $this->user_course_service->isCourseInUsed($course_id);
  }
   
  /**
   * @param int $course_id
   * @return void
  */
  private function deleteOldCourseSchedules(int $course_id)
  {
    return CourseSchedule::where('course_id', $course_id)->delete();
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
        'type_id'             => $lesson['type_id'],
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