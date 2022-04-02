<?php

namespace App\Domain\Content\Models;

use App\Domain\Content\Models\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseComment extends Model
{
    use SoftDeletes;

    public function course()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }
}