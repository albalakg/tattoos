<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\CourseLessonDetail;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes;
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $appends = ['videoSrc'];

    public function getVideoSrcAttribute()
    {
        return 'https://' . config('filesystems.only_bucket') . '.s3.amazonaws.com/' . $this->video_path;  
    }   

    public function lessons()
    {
        return $this->hasOne(CourseLessonDetail::class, 'video_id', 'id')
                    ->with('lesson');
    }
}