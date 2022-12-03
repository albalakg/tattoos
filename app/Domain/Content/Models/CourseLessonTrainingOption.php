<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Content\Models\TrainingOption;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseLessonTrainingOption extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];
    
    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function lesson()
    {
        return $this->hasOne(TrainingOption::class, 'id', 'course_lesson_id');
    }
}