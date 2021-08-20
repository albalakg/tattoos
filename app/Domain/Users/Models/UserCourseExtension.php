<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserCourse;
use Illuminate\Database\Eloquent\Model;

class UserCourseExtension extends Model
{
  public function userCourse()
  {
    return $this->hasOne(UserCourse::class, 'id', 'user_course_id');
  }

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'created_by');
  }
}