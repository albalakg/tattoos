<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domain\Content\Models\CourseLessonTerm;

class Term extends Model
{
    use SoftDeletes;
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function lessons()
    {
        return $this->hasMany(CourseLessonTerm::class, 'term_id', 'id')
                    ->with('lesson');
    }
}