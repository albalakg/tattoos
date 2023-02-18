<?php

namespace App\Domain\Content\Models;

use App\Domain\Content\Models\CourseLesson;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseScheduleLesson extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    
    protected $guarded = [];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];

    public function lesson()
    {
        return $this->hasOne(CourseLesson::class, 'id', 'course_lesson_id');
    }
}