<?php

namespace App\Domain\Courses\Models;

use App\Domain\Tags\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Courses\Models\CourseLesson;

class CourseLessonTag extends Model
{
    public function lesson()
    {
        return $this->hasOne(CourseLesson::class, 'id', 'course_lesson_id');
    }

    public function tag()
    {
        return $this->hasOne(Tag::class, 'id', 'tag_id');
    }
}