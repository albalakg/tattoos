<?php

namespace App\Domain\General\Models;

use Illuminate\Database\Eloquent\Model;

class LuContentType extends Model
{
    const   COURSE      = 1,
            COURSE_AREA = 2,
            LESSON      = 3;

    const ALL_CONTENT_TYPES = [
        self::COURSE,
        self::COURSE_AREA,
        self::LESSON
    ];

    const CONTENT_TYPES_NAME = [
        self::COURSE => 'course',
        self::COURSE_AREA => 'course area',
        self::LESSON => 'lesson'
    ];
}