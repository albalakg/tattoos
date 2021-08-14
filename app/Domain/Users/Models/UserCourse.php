<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\User;
use App\Domain\Courses\Models\Course;
use Illuminate\Database\Eloquent\Model;


class UserCourse extends Model
{
  public function user()
  {
    return $this->hasOne(User::class);
  }

  public function course()
  {
    return $this->hasOne(Course::class);
  }
}