<?php

namespace App\Domain\Content\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\CourseLessonDetail;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes;
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $appends = ['video'];

    public function getVideoAttribute()
    {
        return config('app.url') . '/' . 'files/videos/' . $this->video_path;  
    }

    public function lessons()
    {
        return $this->hasOne(CourseLessonDetail::class, 'video_id', 'id')
                    ->with('lesson');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}