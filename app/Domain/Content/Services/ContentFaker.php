<?php

namespace App\Domain\Content\Services;

use Exception;
use Illuminate\Support\Str;

class ContentFaker
{    
  const COURSE_NAMES = [
    'כדורסל', 'כדורגל', 'בייסבול', 'פוטבול', 'רוגבי'
  ];

  const COURSE_AREA_NAMES = [
    'כושר', 'תזונה', 'טכניקה', 'שינה', 'פסיכולוגיה', 'מנהיגות', 'משחק'
  ];

  const LESSON_NAMES = [
    'פתיחה', 'בסיס', 'מתקדם', 'מעמיק', 'יצירתיות'
  ];
  
  /**
   * @return string
  */
  static public function getRandomCourseName(): string
  {
    return self::COURSE_NAMES[random_int(0, count(self::COURSE_NAMES) - 1)];
  }
  
  /**
   * @return string
  */
  static public function getRandomCourseAreaName(): string
  {
    return self::COURSE_AREA_NAMES[random_int(0, count(self::COURSE_AREA_NAMES) - 1)];
  }
  
  /**
   * @return string
  */
  static public function getRandomLessonName(): string
  {
    return self::LESSON_NAMES[random_int(0, count(self::LESSON_NAMES) - 1)];
  }
}