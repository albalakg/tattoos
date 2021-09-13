<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    public function course()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }
}