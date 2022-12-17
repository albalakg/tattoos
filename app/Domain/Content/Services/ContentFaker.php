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

  const SKILL_NAMES = [
    'כדרור', 'מסירה', 'עצירת כדור', 'הטעייה', 'הדיפה', 'בעיטה', 'הרמה'
  ];

  const TERM_NAMES = [
    'מונח אחד', 'מונח שני', 'מונח שלישי', 'מונח רביעי', 'מונח חמישי'
  ];

  const EQUIPMENT_NAMES = [
    'קונוס', 'כדור', 'שער', 'דשא', 'מגרש'
  ];

  const DESCRIPTIONS = [
    'תוכן אקראי בשביל למלא מקום',
    'לא משהו רציני סתם שטות',
    'תיאור לתוכן שיש מעלי',
    'סתם משפט תיאור בסיסי לא משהו מיוחד'
  ];

  const LESSON_CONTENT = [
    'לורם איפסום דולור סיט אמט, קונסקטורר אדיפיסינג אלית. סת אלמנקום ניסי נון ניבאה. דס איאקוליס וולופטה דיאם. וסטיבולום אט דולור, קראס אגת לקטוס וואל אאוגו וסטיבולום סוליסי טידום בעליק. קולהע צופעט למרקוח איבן איף, ברומץ כלרשט מיחוצים. קלאצי קולהע צופעט למרקוח איבן איף, ברומץ כלרשט מיחוצים. קלאצי קונדימנטום קורוס בליקרה, נונסטי קלובר בריקנה סטום, לפריקך תצטריק לרטי.',
    'קולורס מונפרד אדנדום סילקוף, מרגשי ומרגשח. עמחליף סחטיר בלובק. תצטנפל בלינדו למרקל אס לכימפו, דול, צוט ומעיוט - לפתיעם ברשג - ולתיעם גדדיש. קוויז דומור ליאמום בלינך רוגצה. לפמעט מוסן מנת. קוואזי במר מודוף. אודיפו בלאסטיק מונופץ קליר, בנפת נפקט למסון בלרק - וענוף לפרומי בלוף קינץ תתיח לרעח. לת צשחמי צש בליא, מנסוטו צמלח לביקו ננבי, צמוקו בלוקריה שיצמה ברורק.',
    'לפרומי בלוף קינץ תתיח לרעח. לת צשחמי צש בליא, מנסוטו צמלח לביקו ננבי, צמוקו בלוקריה שיצמה ברורק. ליבם סולגק. בראיט ולחת צורק מונחף, בגורמי מגמש. תרבנך וסתעד לכנו סתשם השמה - לתכי מורגם בורק? לתיג ישבעס.',
    'נולום ארווס סאפיאן - פוסיליס קוויס, אקווזמן גולר מונפרר סוברט לורם שבצק יהול, לכנוץ בעריר גק ליץ, ושבעגט ליבם סולגק. בראיט ולחת צורק מונחף, בגורמי מגמש. תרבנך וסתעד לכנו סתשם השמה - לתכי מורגם בורק? לתיג ישבעס.'
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
  
  /**
   * @return string
  */
  static public function getSkillName(): string
  {
    return self::SKILL_NAMES[random_int(0, count(self::SKILL_NAMES) - 1)];
  }
  
  /**
   * @return string
  */
  static public function getTermName(): string
  {
    return self::TERM_NAMES[random_int(0, count(self::TERM_NAMES) - 1)];
  }
  
  /**
   * @return string
  */
  static public function getEquipmentName(): string
  {
    return self::EQUIPMENT_NAMES[random_int(0, count(self::EQUIPMENT_NAMES) - 1)];
  }
  
  /**
   * @return string
  */
  static public function getLessonContent(): string
  {
    return self::LESSON_CONTENT[random_int(0, count(self::LESSON_CONTENT) - 1)];
  }
}