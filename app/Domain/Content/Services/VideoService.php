<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Content\Models\Video;
use App\Domain\Helpers\StatusService;
use App\Domain\Interfaces\IContentService;
use Illuminate\Database\Eloquent\Collection;

class VideoService implements IContentService
{
  const FILES_PATH = 'content/videos';

  /**
   * @var LogService
  */
  private $log_service;

  /**
   * @var CourseLessonService|null
  */
  private $course_lesson_service;
  
  public function __construct(CourseLessonService $course_lesson_service = null)
  {
    $this->course_lesson_service = $course_lesson_service;
    $this->log_service = new LogService('videos');
  }
    
  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return Video::select(
                'id',
                'name',
                'status',
                'description',
                'video_path',
                'created_at',
              )
              ->orderBy('id', 'desc')
              ->get();
  }
    
  /**
   * @param object $video_data
   * @param int $created_by
   * @return Video
  */
  public function create(object $video_data, int $created_by): ?Video
  {
    $video               = new Video;
    $video->name         = $video_data->name;
    $video->description  = $video_data->description;
    $video->status       = StatusService::ACTIVE;
    $video->video_path   = FileService::create($video_data->file, self::FILES_PATH);
    $video->created_by   = $created_by;
    $video->save();

    return $video;
  }
    
  /**
   * @param object $video_data
   * @param int $updated_by
   * @return Video
  */
  public function update(object $video_data, int $updated_by): ?Video
  {
    if(!$video = Video::find($video_data->id)) {
      throw new Exception('Video not found');
    }

    $video->name         = $video_data->name;
    $video->description  = $video_data->description;
    $video->status       = $video_data->status;

    if(!empty($video_data->file)) {
      FileService::delete($video->video_path);
      $video->video_path   = FileService::create($video_data->file, self::FILES_PATH);
    }

    $video->save();
    return $video;
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
    if(!$video = Video::find($video_id)) {
      throw new Exception('Video not found');
    }

    if($this->isVideoInUsed($video_id)) {
      $this->error_data = $this->course_lesson_service->getLessonsWithVideo($video_id);
      throw new Exception('Cannot delete video that is being used');
    }

    FileService::delete($video->video_path);
    $video->delete();
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