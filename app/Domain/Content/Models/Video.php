<?php

namespace App\Domain\Content\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\CourseLessonDetail;

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