<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Content\Models\CourseLesson;
use App\Domain\Helpers\StatusService;
use App\Domain\Interfaces\IContentService;

class CourseLessonService implements IContentService
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
   * @return Lesson|null
  */
  public function getLessonsWithCourseArea(int $course_area_id): ?CourseLesson
  {
    return CourseLesson::where('$course_area_id', $course_area_id)
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
    return CourseLesson::query()
              ->join('course_areas', 'course_areas.id', 'course_lessons.course_area_id')
              ->join('courses', 'courses.id', 'course_lessons.course_id')
              ->join('course_categories', 'course_categories.id', 'courses.category_id')
              ->join('videos', 'videos.id', 'course_lessons.video_id')
              ->select(
                'course_lessons.id',
                'course_lessons.name',
                'course_lessons.status',
                'course_lessons.created_at',
                'courses.name AS course_name',
                'course_areas.name AS course_area_name',
                'course_categories.name AS course_category_name',
                'videos.video_path',
              )
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
    $lesson               = new CourseLesson;
    $lesson->category_id  = $lessonData->category_id;
    $lesson->name         = $lessonData->name;
    $lesson->description  = $lessonData->description;
    $lesson->price        = $lessonData->price;
    $lesson->discount     = $lessonData->discount;
    $lesson->view_order   = 0;
    $lesson->status       = StatusService::PENDING;
    $lesson->image        = FileService::create($lessonData->image, self::FILES_PATH);
    $lesson->trailer      = FileService::create($lessonData->trailer, self::FILES_PATH);
    $lesson->created_by   = $created_by;
    $lesson->save();

    return $lesson;
  }

  /**
   * @param object $lessonData
   * @param int $updated_by
   * @return CourseLesson|null
  */
  public function update(object $lessonData, int $updated_by): ?CourseLesson
  {
    if(!$lesson = CourseLesson::find($lessonData->id)) {
      throw new Exception('CourseLesson not found');
    };

    $lesson->category_id  = $lessonData->category_id;
    $lesson->name         = $lessonData->name;
    $lesson->description  = $lessonData->description;
    $lesson->price        = $lessonData->price;
    $lesson->discount     = $lessonData->discount;
    $lesson->view_order   = 0;
    $lesson->status       = $lessonData->status;
    $lesson->updated_by   = $updated_by;
    
    if(!empty($lesson->image)) {
      $lesson->image        = FileService::create($lessonData->image, self::FILES_PATH);
    }

    if(!empty($lesson->trailer)) {
      $lesson->trailer        = FileService::create($lessonData->trailer, self::FILES_PATH);
    }
    
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
    foreach($ids AS $video_id) {
      if($error = $this->delete($video_id, $deleted_by)) {
        return $error;
      }
    }
  } 
  
  /**
   * @param int $video_id
   * @param int $deleted_by
   * @return void
  */
  public function delete(int $video_id, int $deleted_by)
  {
    try {
      
    } catch(Exception $ex) {

    }
  }
    
  /**
   * @param int $course_area_id
   * @return bool
  */
  public function isCourseAreaInUsed(int $course_area_id): bool
  {
    return CourseLesson::where('course_area_id', $course_area_id)->exists();
  }
}