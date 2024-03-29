<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\UserCourse;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\CourseSchedule;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCourseSchedule extends Model
{
  use SoftDeletes;

  protected $guarded = [];

  public $timestamps = false;

  public function lessons()
  {
    return $this->hasMany(UserCourseScheduleLesson::class, 'user_course_schedule_id', 'id');
  }

  public function courseSchedule()
  {
    return $this->hasMany(CourseSchedule::class, 'id', 'course_schedule_id');
  }

  public function userCourse()
  {
    return $this->hasMany(UserCourse::class, 'id', 'user_course_id');
  }
}