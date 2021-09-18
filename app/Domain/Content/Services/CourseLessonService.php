<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Content\Models\Video;
use App\Domain\Helpers\StatusService;
use App\Domain\Content\Models\CourseLesson;

class CourseLessonService
{
  const FILES_PATH = 'content/lessons';

  /**
   * @var LogService
  */
  private $log_service;
  
  public function __construct()
  {
      $this->log_service = new LogService('videos');
  }
  
  /**
   * @param int $video_id
   * @return CourseLesson
  */
  public function getLessonsWithVideo(int $video_id): CourseLesson
  {
    return CourseLesson::where('video_id', $video_id)
                      ->select('id', 'name', 'status', 'video_id')
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
}