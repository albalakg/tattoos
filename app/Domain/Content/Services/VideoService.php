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
     * @param object $videoData
     * @param int $created_by
     * @return Video
    */
    public function createVideo(object $videoData, int $created_by): ?Video
    {
        

        return null;
    }
}