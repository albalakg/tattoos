<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Models\UserCourse;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Users\Models\UserCourseSubmissionComment;

class UserCourseSubmission extends Model
{
  public function userCourse()
  {
    return $this->hasOne(UserCourse::class, 'id', 'user_course_id');
  }

  public function course()
  {
    return $this->hasOne(UserCourse::class, 'id', 'user_course_id')
                ->join('courses', 'courses.id', 'user_courses.course_id')
                ->select('user_course_id', 'courses.name', 'user_courses.course_id');
  }

  public function user()
  {
    return $this->hasOne(UserCourse::class, 'id', 'user_course_id')
                ->join('user_details', 'user_details.user_id', 'user_courses.user_id')
                ->select(
                  'user_course_id',
                  'user_courses.user_id',
                  'user_details.first_name',
                  'user_details.last_name',
                );
  }

  public function comments()
  {
    return $this->hasMany(UserCourseSubmissionComment::class, 'user_course_submission_id', 'id')
                ->orderBy('created_at', 'desc');
  }
}