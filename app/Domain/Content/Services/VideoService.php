<?php

namespace App\Domain\Content\Services;

use App\Domain\Helpers\LogService;
use App\Domain\Content\Models\Video;

class VideoService
{
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
              )
              ->simplePaginate(1000);
  }
    
    /**
     * @param object $videoData
     * @param int $created_by
     * @return Video
    */
    public function createVideo(object $videoData, int $created_by): ?Video
    {
        return null;
    }
}