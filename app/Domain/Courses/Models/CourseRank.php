<?php

namespace App\Domain\Courses\Models;

use App\Domain\Users\Models\User;
use App\Domain\Courses\Models\Course;
use Illuminate\Database\Eloquent\Model;

class CourseRank extends Model
{
    public function course()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}