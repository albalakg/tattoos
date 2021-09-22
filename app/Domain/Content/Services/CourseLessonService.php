<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Helpers\StatusService;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Interfaces\IContentService;
use App\Domain\Content\Models\CourseLesson;
use Illuminate\Database\Eloquent\Collection;

class CourseLessonService implements IContentService
{
  const FILES_PATH = 'content/course-lessons';

  /**
   * @var LogService
  */
  private $log_service;

  /**
   * @var CourseAreaService
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
   * @param int $video_id
   * @return bool
  */
  public function isVideoInUsed(int $video_id): bool
  {
    return CourseLesson::where('video_id', $video_id)->exists();
  }

  /**
   * @return object
  */
  public function getAll(): object
  {
    return $this->baseQueryBuilder()
              ->orderBy('course_lessons.created_at', 'desc')
              ->simplePaginate(1000);
  }
    
  /**
   * @param object $lessonData
   * @param int $created_by
   * @return CourseLesson|null
  */
  public function create(object $lessonData, int $created_by): ?CourseLesson
  {
    $lesson                   = new CourseLesson;
    $lesson->course_id        = $this->course_area_service->getById($lessonData->course_area_id)->course_id;
    $lesson->course_area_id   = $lessonData->course_area_id;
    $lesson->video_id         = $lessonData->video_id;
    $lesson->name             = $lessonData->name;
    $lesson->content          = $lessonData->content;
    $lesson->status           = StatusService::PENDING;
    $lesson->save();

    return $this->baseQueryBuilder()
          ->where('course_lessons.id', $lesson->id)
          ->first();
  }

  /**
   * @param object $lessonData
   * @param int $updated_by
   * @return CourseLesson|null
  */
  public function update(object $lessonData, int $updated_by): ?CourseLesson
  {
    if(!$lesson = CourseLesson::find($lessonData->id)) {
      throw new Exception('Course Lesson not found');
    };

    $lesson->course_id      = $this->course_area_service->getById($lessonData->course_area_id)->course_id;
    $lesson->course_area_id = $lessonData->course_area_id;
    $lesson->name           = $lessonData->name;
    $lesson->content        = $lessonData->content;
    $lesson->status         = $lessonData->status;
    
    $lesson->save();
    return $lesson;
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
   * @param int $lesson_id
   * @param int $deleted_by
   * @return void
  */
  public function delete(int $lesson_id, int $deleted_by)
  {
    CourseLesson::where('id', $lesson_id)->delete();
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
            ->join('course_areas', 'course_areas.id', 'course_lessons.course_area_id')
            ->join('courses', 'courses.id', 'course_lessons.course_id')
            ->join('course_categories', 'course_categories.id', 'courses.category_id')
            ->join('videos', 'videos.id', 'course_lessons.video_id')
            ->select(
              'course_lessons.id',
              'course_lessons.course_id',
              'course_lessons.video_id',
              'course_lessons.course_area_id',
              'course_lessons.name',
              'course_lessons.content',
              'course_lessons.status',
              'course_lessons.created_at',
              'courses.name AS course_name',
              'course_areas.name AS course_area_name',
              'course_categories.name AS course_category_name',
              'videos.video_path',
            );
  }
}