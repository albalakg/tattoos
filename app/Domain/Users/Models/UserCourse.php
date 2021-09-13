<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use App\Domain\Content\Models\Course;
use Illuminate\Database\Eloquent\Model;

class UserCourse extends Model
{
  const DONE = 100;
  
  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }

  public function course()
  {
    return $this->hasOne(Course::class, 'id', 'course_id');
  }
}