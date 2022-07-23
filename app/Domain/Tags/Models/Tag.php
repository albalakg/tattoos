<?php

namespace App\Domain\Tags\Models;

use App\Domain\Content\Models\Course;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\CourseTag;
use App\Domain\Content\Models\CourseLessonTag;

class Tag extends Model
{
  public function courses()
  {
    return $this->hasMany(CourseTag::class, 'tag_id', 'id')
                ->with('course');
  }

  public function lessons()
  {
    return $this->hasMany(CourseLessonTag::class, 'tag_id', 'id')
                ->with('lesson');
  }
}