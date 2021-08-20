<?php

namespace App\Domain\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Courses\Models\CourseArea;
use App\Domain\Courses\Models\CourseLessonTag;
use App\Domain\Courses\Models\CourseLessonRank;
use App\Domain\Courses\Models\CourseLessonComment;

class CourseLesson extends Model
{
    public function area()
    {
        return $this->hasOne(CourseArea::class, 'id', 'course_area_id');
    }

    public function ranks()
    {
        return $this->hasMany(CourseLessonRank::class, 'course_lesson_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany(CourseLessonTag::class, 'course_lesson_tag_id', 'id')
                    ->with('tag');
    }

    public function comments()
    {
        return $this->hasMany(CourseLessonComment::class, 'course_lesson_tag_id', 'id');
    }
}