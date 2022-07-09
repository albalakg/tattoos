<?php

namespace App\Domain\Content\Services;

use Exception;
use Illuminate\Support\Str;

class ContentFaker
{    
  const COURSE_CATEGORY_NAMES = [
    'כדורסל', 'כדורגל', 'בייסבול', 'פוטבול', 'רוגבי'
  ];

  const COURSE_NAMES = [
    'כדורסל בסיסי', 'כדורגל בסיסי', 'בייסבול בסיסי', 'פוטבול בסיסי', 'רוגבי בסיסי'
  ];

  const COURSE_AREA_NAMES = [
    'כושר', 'תזונה', 'טכניקה', 'שינה', 'פסיכולוגיה', 'מנהיגות', 'משחק'
  ];

  const LESSON_NAMES = [
    'פתיחה', 'בסיס', 'מתקדם', 'מעמיק', 'יצירתיות'
  ];

  const TRAINER_NAMES = [
    'גל', 'אופק', 'נדב', 'שון', 'אייל', 'דנה', 'אביגיל'
  ];

  const VIDEO_NAMES = [
    'סרטון בסיסי קצר', 'סרטון בסיסי מוזר', 'סרטון מיוחד', 'סרטון משונה', 'סרטון ענק', 'סרטון טירוף'
  ];

  const DESCRIPTIONS = [
    'תוכן אקראי בשביל למלא מקום',
    'לא משהו רציני סתם שטות',
    'תיאור לתוכן שיש מעלי',
    'סתם משפט תיאור בסיסי לא משהו מיוחד'
  ];
  
  /**
   * @return string
  */
  static public function getCourseCategoryName(): string
  {
    return self::COURSE_CATEGORY_NAMES[random_int(0, count(self::COURSE_CATEGORY_NAMES) - 1)];
  }
  
  
  /**
   * @return string
  */
  static public function getCourseName(): string
  {
    return self::COURSE_NAMES[random_int(0, count(self::COURSE_NAMES) - 1)];
  }
  
  /**
   * @return string
  */
  static public function getCourseAreaName(): string
  {
    return self::COURSE_AREA_NAMES[random_int(0, count(self::COURSE_AREA_NAMES) - 1)];
  }
  
  /**
   * @return string
  */
  static public function getLessonName(): string
  {
    return self::LESSON_NAMES[random_int(0, count(self::LESSON_NAMES) - 1)];
  }
  
  /**
   * @return string
  */
  static public function getTitle(): string
  {
    return 'מאמן ' . self::COURSE_AREA_NAMES[random_int(0, count(self::COURSE_AREA_NAMES) - 1)];
  }
  
  /**
   * @return string
  */
  static public function getTrainerName(): string
  {
    return self::TRAINER_NAMES[random_int(0, count(self::TRAINER_NAMES) - 1)];
  }
  
  /**
   * @return string
  */
  static public function getDescription(): string
  {
    return self::DESCRIPTIONS[random_int(0, count(self::DESCRIPTIONS) - 1)];
  }
  
  /**
   * @return string
  */
  static public function getVideoName(): string
  {
    return self::VIDEO_NAMES[random_int(0, count(self::VIDEO_NAMES) - 1)];
  }
}