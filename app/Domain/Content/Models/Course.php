<?php

namespace App\Domain\Content\Models;

use App\Domain\Helpers\StatusService;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\CourseArea;
use App\Domain\Content\Models\CourseRank;
use App\Domain\Content\Models\CourseView;
use App\Domain\Content\Models\CourseComment;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    protected $appends = ['trailerSrc', 'imageSrc'];

    public function getImageSrcAttribute()
    {
        return config('app.url') . '/' . 'files/' . $this->image;  
    }

    public function getTrailerSrcAttribute()
    {
        return config('app.url') . '/' . 'files/' . $this->trailer;  
    }

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

    public function areasWithLessons()
    {
        return $this->hasMany(CourseArea::class, 'course_id', 'id')
                    ->with('lessons');
    }

    public function activeAreasWithLessons()
    {
        return $this->hasMany(CourseArea::class, 'course_id', 'id')
                    ->where('status', StatusService::ACTIVE)
                    ->with('lessons');
    }

    public function activeLessons()
    {
        return $this->hasMany(CourseLesson::class, 'course_id', 'id')
                    ->where('status', StatusService::ACTIVE);
    }

    public function inactiveLessons()
    {
        return $this->hasMany(CourseLesson::class, 'course_id', 'id')
                    ->where('status', StatusService::INACTIVE);
    }

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class, 'course_id', 'id');
    }

    public function areasWithActiveLessons()
    {
        return $this->hasMany(CourseArea::class, 'course_id', 'id')
                    ->with('activeLessons');
    }

    public function areasWithInactiveLessons()
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