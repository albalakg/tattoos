<?php

namespace App\Domain\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domain\Users\Models\UserCourseSchedule;

class UserCourseScheduleLesson extends Model
{
  use SoftDeletes;

  protected $guarded = [];

  public $timestamps = false;

  public function userCourseSchedule()
  {
    return $this->hasOne(UserCourseSchedule::class, 'id', 'user_course_schedule_id');
  }
}