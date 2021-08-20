<?php

namespace App\Domain\Courses\Models;

use App\Domain\Helpers\StatusService;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Courses\Models\CourseArea;
use App\Domain\Courses\Models\CourseRank;
use App\Domain\Courses\Models\CourseView;
use App\Domain\Courses\Models\CourseComment;

class Course extends Model
{
    public function areas()
    {
        return $this->hasMany(CourseArea::class, 'course_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany(CourseTag::class, 'course_id', 'id')
                    ->with('tag');
    }

    public function comments()
    {
        return $this->hasMany(CourseComment::class, 'course_id', 'id');
    }

    public function ranks()
    {
        return $this->hasMany(CourseRank::class, 'course_id', 'id');
    }

    public function views()
    {
        return $this->hasMany(CourseView::class, 'course_id', 'id');
    }

    public function activeAreas()
    {
        return $this->hasMany(CourseArea::class, 'course_id', 'id')
                    ->where('status', StatusService::ACTIVE);
    }

    public function inactiveAreas()
    {
        return $this->hasMany(CourseArea::class, 'course_id', 'id')
                    ->where('status', StatusService::INACTIVE);
    }

    public function comingSoonAreas()
    {
        return $this->hasMany(CourseArea::class, 'course_id', 'id')
                    ->where('status', StatusService::PENDING);
    }

    public function lessons()
    {
        return $this->hasMany(CourseArea::class, 'course_id', 'id')
                    ->where('status', StatusService::ACTIVE)
                    ->with('lessons');
    }

    public function activeLessons()
    {
        return $this->hasMany(CourseArea::class, 'course_id', 'id')
                    ->with('activeLessons');
    }

    public function inactiveLessons()
    {
        return $this->hasMany(CourseArea::class, 'course_id', 'id')
                    ->with('inactiveLessons');
    }

    public function comingSoonLessons()
    {
        return $this->hasMany(CourseArea::class, 'course_id', 'id')
                    ->with('comingSoonLessons');
    }
}