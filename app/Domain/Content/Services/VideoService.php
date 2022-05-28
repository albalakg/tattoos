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
   * @return null|Video
  */
  public function getRandomVideo(): ?Video
  {
    return Video::inRandomOrder()->first();
  }
      
  /**
   * Fully deletes all of the content
   *
   * @return void
  */
  public function truncate()
  {
    $videos_ids = Video::pluck('id');
    foreach($videos_ids AS $video_id) {
      $this->forceDelete($video_id, 0);
    }
  }

  /**
   * @param array $data
   * @param int $created_by
   * @return Video
  */
  public function create(array $data, int $created_by): ?Video
  {
    $video               = new Video;
    $video->name         = $data['name'];
    $video->description  = $data['description'];
    $video->video_length = $data['video_length'];
    $video->status       = StatusService::ACTIVE;
    $video->video_path   = FileService::create($data['file'], self::FILES_PATH);
    $video->created_by   = $created_by;

    $video->save();

    $this->log_service->info('Video has been created: ' . json_encode($video));

    return $video;
  }
    
  /**
   * @param array $data
   * @param int $updated_by
   * @return Video
  */
  public function update(array $data, int $updated_by): ?Video
  {
    if(!$video = Video::find($data['id'])) {
      throw new Exception('Video not found');
    }

    $video->name         = $data['name'];
    $video->description  = $data['description'];
    $video->status       = $data['status'];

    if(!empty($data['file'])) {
      FileService::delete($video->video_path);
      $video->video_path   = FileService::create($data['file'], self::FILES_PATH);
      $video->video_length = $data['video_length'];
    }

    $video->save();
    
    $this->log_service->info('Video has been updated: ' . json_encode($video));

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
   * Soft delete the item
   * @param int $video_id
   * @param int $deleted_by
   * @return bool
  */
  public function delete(int $video_id, int $deleted_by): bool
  {
    if(!$video = $this->canDelete($video_id)) {
      return false;
    }

    $result = $video->delete();
    $this->log_service->info('Video ' . $video_id . ' has been deleted');
    return $result;
  }
  
  /**
   * @param int $video_id
   * @param int $deleted_by
   * @return bool
  */
  public function forceDelete(int $video_id, int $deleted_by): bool
  {
    if(!$video = $this->canDelete($video_id)) {
      return false;
    }

    FileService::delete($video->video_path);
    $result = $video->forceDelete();
    $this->log_service->info('Video ' . $video_id . ' has been forced deleted');
    return $result;
  }
  
  /**
   * @param int $video_id
   * @return Video
  */
  private function canDelete(int $video_id): Video
  {
    if(!$video = Video::find($video_id)) {
      throw new Exception('Video not found');
    }

    if($this->isVideoInUsed($video_id)) {
      $this->error_data = $this->course_lesson_service->getLessonsWithVideo($video_id);
      throw new Exception('Cannot delete video that is being used');
    }

    return $video;
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