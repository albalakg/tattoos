<?php

namespace App\Domain\Content\Services;

use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Content\Models\Video;
use App\Domain\Helpers\StatusService;
use Exception;

class VideoService
{
  const FILES_PATH = 'content/videos';

  /**
   * @var LogService
  */
  private $log_service;

  /**
   * @var CourseLessonService
  */
  private $course_lesson_service;
  
  public function __construct(CourseLessonService $course_lesson_service = null)
  {
    $this->course_lesson_service = $course_lesson_service;
    $this->log_service = new LogService('videos');
  }
    
  /**
   * @return object
  */
  public function getAll(): object
  {
    return Video::select(
                'videos.id',
                'videos.name',
                'videos.status',
                'videos.description',
                'videos.video_path',
                'videos.created_at',
              )
              ->orderBy('videos.created_at', 'desc')
              ->simplePaginate(1000);
  }
    
  /**
   * @param object $videoData
   * @param int $created_by
   * @return Video
  */
  public function createVideo(object $videoData, int $created_by): ?Video
  {
    $video               = new Video;
    $video->name         = $videoData->name;
    $video->description  = $videoData->description;
    $video->status       = StatusService::ACTIVE;
    $video->video_path   = FileService::create($videoData->file, self::FILES_PATH);
    $video->created_by   = $created_by;
    $video->save();

    return $video;
  }
    
  /**
   * @param object $videoData
   * @param int $created_by
   * @return Video
  */
  public function updateVideo(object $videoData, int $created_by): ?Video
  {
    if(!$video = Video::find($videoData->id)) {
      throw new Exception('Video not found');
    }

    $video->name         = $videoData->name;
    $video->description  = $videoData->description;
    $video->status       = $videoData->status;
    if(!empty($videoData->file)) {
      $this->deleteVideoFile($video);
      $video->video_path   = FileService::create($videoData->file, self::FILES_PATH);
    }

    $video->save();
    return $video;
  }
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function deleteVideos(array $ids, int $deleted_by)
  {
    foreach($ids AS $video_id) {
      if($error = $this->deleteVideo($video_id, $deleted_by)) {
        return $error;
      }
    }
  } 

  private function deleteVideo(int $video_id, int $deleted_by)
  {
    try {
      if($this->isVideoInUsed($video_id)) {
        throw new Exception('Cannot delete video, in use');
      }
  
      if(!$video = Video::find($video_id)) {
        throw new Exception('Video not found');
      }
  
      $this->deleteVideoFile($video);

      $video->delete();
      
    } catch(Exception $ex) {
      return $this->course_lesson_service->getLessonsWithVideo($video_id);
    }
  }
  
  /**
   * Delete the video file from the storage
   *
   * @param Video $video
   * @return bool
  */
  private function deleteVideoFile(Video $video)
  {
    return FileService::delete($video->video_path);
  } 
  
  /**
   * @param int $video_id
   * @return bool
  */
  private function isVideoInUsed($video_id): bool
  {
    return $this->course_lesson_service->isVideoInUsed($video_id);
  }
}