<?php

namespace App\Domain\Users\Models;

use Illuminate\Database\Eloquent\Model;
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
}