<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Content\Models\Video;
use App\Domain\Helpers\StatusService;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Interfaces\IContentService;
use App\Domain\Content\Models\CourseLesson;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Helpers\DataManipulationService;

class CourseLessonService implements IContentService
{
  const FILES_PATH = 'content/lessons';

  /**
   * @var LogService
  */
  private $log_service;

  /**
   * @var CourseAreaService|null
  */
  private $course_area_service;
  
  public function __construct(CourseAreaService $course_area_service = null)
  {
    $this->course_area_service = $course_area_service;
    $this->log_service = new LogService('courseLessons');
  }
  
  /**
   * @param int $video_id
   * @return CourseLesson
  */
  public function getLessonsWithVideo(int $video_id): CourseLesson
  {
    return CourseLesson::where('video_id', $video_id)
                      ->select('id', 'name', 'status')
                      ->get();
  }
  
  /**
   * @param int $lesson_id
   * @return Video
  */
  public function getVideoByLessonId(int $lesson_id): Video
  {
    $lesson = CourseLesson::where('id', $lesson_id)
                      ->with('video')
                      ->select('id', 'video_id')
                      ->first();

    return $lesson->video;
  }
  
  /**
   * @param int $course_area_id
   * @return Collection|null
  */
  public function getLessonsOfCourseArea(int $course_area_id): ?Collection
  {
    return CourseLesson::where('course_area_id', $course_area_id)
                      ->select('id', 'name', 'status')
                      ->get();
  }
  
  /**
   * @param array|int $lessons_ids
   * @return Collection|null
  */
  public function getLessonsByIds($lessons_ids): ?Collection
  {
    $lessons_ids = DataManipulationService::intToArray($lessons_ids);

    return CourseLesson::whereIn('id', $lessons_ids)
                      ->select('id', 'course_id', 'course_area_id', 'name', 'status', 'image')
                      ->get();
  }

  /**
   * @param int $course_id
   * @return Collection
  */
  public function getLessonsDurationByCourseId(int $course_id): Collection
  {
    return CourseLesson::where('course_id', $course_id)
                        ->join('videos', 'videos.id', 'course_lessons.video_id')
                        ->select('course_lessons.id', 'video_length')
                        ->get();
  }

  /**
   * @param int $lesson_id
   * @return bool
  */
  public function getLessonCourseId(int $lesson_id): bool
  {
    return CourseLesson::where('id', $lesson_id)->value('course_id');
  }

  /**
   * @param int $video_id
   * @return bool
  */
  public function isVideoInUsed(int $video_id): bool
  {
    return CourseLesson::where('video_id', $video_id)->exists();
  }

  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return $this->baseQueryBuilder()
              ->orderBy('course_lessons.id', 'desc')
              ->get();
  }

  /**
   * @return Collection
   * 
   * @param int $lessons
   * @param int $status
  */
  public function getRandomActiveLessons(int $lessons = 4, int $status = 1): Collection
  {
    return CourseLesson::query()
              ->limit($lessons)
              ->inRandomOrder()
              ->where('status', $status)
              ->select('name', 'content', 'image')
              ->get();
  }
  
  /**
   * @param array $lessons_ids
   * @param int $course_area_id
   * @param int $course_id
   * @return bool
  */
  public function assignCourseArea(array $lessons_ids, int $course_area_id, int $course_id): bool
  {
    $lessons_found = CourseLesson::whereIn('id', $lessons_ids)->count();
    if($lessons_found !== count($lessons_ids)) {
      return false;
    }

    CourseLesson::whereIn('id', $lessons_ids)->update([
      'course_id'       => $course_id,
      'course_area_id'  => $course_area_id
    ]);
    
    return true;
  }
  
  /**
   * @param array $lessons_ids
   * @return bool
  */
  public function unAssignLessons(array $lessons_ids): bool
  {
    $lessons_found = CourseLesson::whereIn('id', $lessons_ids)->count();
    if($lessons_found !== count($lessons_ids)) {
      return false;
    }

    CourseLesson::whereIn('id', $lessons_ids)->update([
      'course_id'       => null,
      'course_area_id'  => null
    ]);
    
    return true;
  }
        
  /**
   * Gets the next view order of the course area
   *
   * @param int $course_area_id
   * @return int
  */
  public function getLessonViewOrder(int $course_area_id): int
  {
    $last_view_order = CourseLesson::where('course_area_id', $course_area_id)->orderBy('view_order', 'desc')->value('view_order');
    return $last_view_order ? ++$last_view_order : 1; 
  }
        
  /**
   * Checks the latest view order and returns the next one
   *
   * @return int
  */
  public function getNextViewOrder(): int
  {
    $last_view_order = CourseLesson::orderBy('view_order', 'desc')->value('view_order');
    return $last_view_order ? $last_view_order++ : 1; 
  }
  
  /**
   * Fully deletes all of the content
   *
   * @return void
  */
  public function truncate()
  {
    $lessons_ids = CourseLesson::withTrashed()->pluck('id');
    foreach($lessons_ids AS $lesson_id) {
      $this->forceDelete($lesson_id, 0);
    }
    CourseLesson::truncate();
  }

  /**
   * @param array $data
   * @param int $created_by
   * @return CourseLesson|null
  */
  public function create(array $data, int $created_by): ?CourseLesson
  {
    $lesson                   = new CourseLesson;
    $lesson->course_id        = $this->course_area_service->getById($data['course_area_id'])->course_id;
    $lesson->course_area_id   = $data['course_area_id'];
    $lesson->video_id         = $data['video_id'];
    $lesson->image            = FileService::create($data['image'], self::FILES_PATH);
    $lesson->name             = $data['name'];
    $lesson->content          = $data['content'];
    $lesson->description      = $data['description'];
    $lesson->rehearsals       = $data['rehearsals']       ?? null;
    $lesson->rest_time        = $data['rest_time']        ?? null;
    $lesson->activity_time    = $data['activity_time']    ?? null;
    $lesson->activity_period  = $data['activity_period']  ?? null;
    $lesson->view_order       = $this->getLessonViewOrder($data['course_area_id']);
    $lesson->status           = $data['status']           ?? StatusService::PENDING;
    $lesson->save();

    $this->log_service->info('Lesson ' . $lesson->id . ' has been created: ' . json_encode($lesson));

    return $this->baseQueryBuilder()
          ->where('course_lessons.id', $lesson->id)
          ->first();
  }

  /**
   * @param array $data
   * @param int $updated_by
   * @return CourseLesson|null
  */
  public function update(array $data, int $updated_by): ?CourseLesson
  {
    if(!$lesson = CourseLesson::find($data['id'])) {
      throw new Exception('Course Lesson not found');
    };

    if(!empty($data['image'])) {
      FileService::delete($lesson->image);
      $lesson->image        = FileService::create($data['image'], self::FILES_PATH);
    }

    $lesson->course_id      = $this->course_area_service->getById($data['course_area_id'])->course_id;
    $lesson->course_area_id = $data['course_area_id'];
    $lesson->name           = $data['name'];
    $lesson->content        = $data['content'];
    $lesson->description    = $data['description'];
    $lesson->status         = $data['status'];
    
    if(!empty($data['rehearsals'])) {
      $lesson->rehearsals = $data['rehearsals'];
    }
    
    if(!empty($data['rest_time'])) {
      $lesson->rest_time = $data['rest_time'];
    }

    if(!empty($data['activity_time'])) {
      $lesson->activity_time = $data['activity_time'];
    }

    if(!empty($data['activity_period'])) {
      $lesson->activity_period = $data['activity_period'];
    }
    
    $lesson->save();

    $this->log_service->info('Lesson ' . $lesson->id . ' has been updated: ' . json_encode($lesson));

    return $lesson;
  }
  
  /**
   * update the view order of the content
   *
   * @param array $lessons
   * @return void
  */
  public function updateOrder(array $lessons)
  {
    foreach($lessons AS $lesson) {
      CourseLesson::where('id', $lesson['id'])->update(['view_order' => $lesson['view_order']]);
    }
  }
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $lesson_id) {
      if($error = $this->delete($lesson_id, $deleted_by)) {
        return $error;
      }
    }
  } 
  
  /**
   * Soft delete the item 
   * @param int $lesson_id
   * @param int $deleted_by
   * @return bool
  */
  public function delete(int $lesson_id, int $deleted_by): bool
  {
    if($lesson = $this->canDelete($lesson_id)) {
      $result = $lesson->delete();
      $this->log_service->info('Lesson ' . $lesson_id . ' has been deleted');
      return $result;
    }
  }
  
  /**
   * @param int $lesson_id
   * @param int $deleted_by
   * @return bool
  */
  public function forceDelete(int $lesson_id, int $deleted_by): bool
  {
    if($lesson = $this->canDelete($lesson_id)) {
      FileService::delete($lesson->image);
      $result = $lesson->forceDelete();
      $this->log_service->info('Lesson ' . $lesson_id . ' has been forced deleted');
      return $result;
    }
  }
    
  /**
   * @param int $lesson_id
   * @return CourseLesson
  */
  private function canDelete(int $lesson_id): CourseLesson
  {
    if(!$lesson = CourseLesson::find($lesson_id)) {
      throw new Exception('Course Lesson not found');
    }

    return $lesson;
  }

  /**
   * @param int $course_area_id
   * @return bool
  */
  public function isCourseAreaInUsed(int $course_area_id): bool
  {
    return CourseLesson::where('course_area_id', $course_area_id)->exists();
  }
  
  /**
   * Build base query
   *
   * @return Builder
  */   
  private function baseQueryBuilder(): Builder
  {
    return CourseLesson::query()
            ->leftJoin('course_areas', 'course_areas.id', 'course_lessons.course_area_id')
            ->leftJoin('courses', 'courses.id', 'course_lessons.course_id')
            ->leftJoin('course_categories', 'course_categories.id', 'courses.category_id')
            ->join('videos', 'videos.id', 'course_lessons.video_id')
            ->select(
              'course_lessons.id',
              'course_lessons.course_id',
              'course_lessons.video_id',
              'course_lessons.course_area_id',
              'course_lessons.name',
              'course_lessons.content',
              'course_lessons.description',
              'course_lessons.rehearsals',
              'course_lessons.activity_time',
              'course_lessons.activity_period',
              'course_lessons.rest_time',
              'course_lessons.status',
              'course_lessons.view_order',
              'course_lessons.created_at',
              'courses.name AS course_name',
              'course_areas.name AS course_area_name',
              'course_categories.name AS course_category_name',
              'videos.video_path',
              'videos.video_length',
            );
  }
}