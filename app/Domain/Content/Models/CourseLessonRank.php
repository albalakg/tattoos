<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\CourseLesson;

class CourseLessonRank extends Model
{
    public function lesson()
    {
        return $this->hasOne(CourseLesson::class, 'id', 'course_lesson_id');
    }
}