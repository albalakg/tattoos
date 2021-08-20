<?php

namespace App\Domain\Courses\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Courses\Models\CourseLessonDetail;

class Video extends Model
{
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