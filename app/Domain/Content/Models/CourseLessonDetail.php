<?php

namespace App\Domain\Content\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\CourseLesson;

class CourseLessonDetail extends Model
{
    public function lesson()
    {
        return $this->hasOne(CourseLesson::class, 'id', 'course_lesson_id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}