<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domain\Content\Models\CourseLessonEquipment;

class Equipment extends Model
{
    use SoftDeletes;
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $appends = ['imageSrc'];

    public function getImageSrcAttribute()
    {
        return config('app.url') . '/' . 'files/' . $this->image;  
    }

    public function lessons()
    {
        return $this->hasMany(CourseLessonEquipment::class, 'equipment_id', 'id')
                    ->with('lesson');
    }
}