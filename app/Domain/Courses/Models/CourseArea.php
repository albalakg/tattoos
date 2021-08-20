<?php

namespace App\Domain\Courses\Models;

use App\Domain\Courses\Models\Course;
use App\Domain\Helpers\StatusService;
use Illuminate\Database\Eloquent\Model;

class CourseArea extends Model
{
    public function course()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }
    
    public function lessons()
    {
        return $this->hasMany(CourseLesson::class, 'course_area_id', 'id');
    }
    
    public function activeLessons()
    {
        return $this->hasMany(CourseLesson::class, 'course_area_id', 'id')
                    ->where('status', StatusService::ACTIVE);
    }
    
    public function inactiveLessons()
    {
        return $this->hasMany(CourseLesson::class, 'course_area_id', 'id')
                    ->where('status', StatusService::INACTIVE);
    }
    
    public function comingSoonLessons()
    {
        return $this->hasMany(CourseLesson::class, 'course_area_id', 'id')
                    ->where('status', StatusService::PENDING);
    }
}