<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\CourseLesson;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseLessonEquipment extends Model
{
    use SoftDeletes;
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function lesson()
    {
        return $this->hasOne(CourseLesson::class, 'id', 'course_lesson_id');
    }
}