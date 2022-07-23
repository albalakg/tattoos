<?php

namespace App\Domain\Content\Models;

use App\Domain\Content\Models\Course;
use Illuminate\Database\Eloquent\Model;

class CourseView extends Model
{
    public function course()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }
}