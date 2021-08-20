<?php

namespace App\Domain\Courses\Models;

use App\Domain\Tags\Models\Tag;
use App\Domain\Courses\Models\Course;
use Illuminate\Database\Eloquent\Model;

class CourseTag extends Model
{
    public function course()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }

    public function tag()
    {
        return $this->hasOne(Tag::class, 'id', 'tag_id');
    }
}