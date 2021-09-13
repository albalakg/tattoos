<?php

namespace App\Domain\Content\Models;

use App\Domain\Tags\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\CourseLesson;

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