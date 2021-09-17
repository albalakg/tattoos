<?php

namespace App\Domain\Content\Services;

use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Content\Models\Video;
use App\Domain\Helpers\StatusService;

class VideoService
{
  const FILES_PATH = 'content/videos';

  /**
   * @var LogService
  */
  private $log_service;
  
  public function __construct()
  {
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
    return null;
  }
}