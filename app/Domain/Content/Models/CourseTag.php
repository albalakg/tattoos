<?php

namespace App\Domain\Content\Models;

use App\Domain\Tags\Models\Tag;
use App\Domain\Content\Models\Course;
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