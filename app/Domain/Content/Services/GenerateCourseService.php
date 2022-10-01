<?php

namespace App\Domain\Content\Services;

use Exception;
use Illuminate\Support\Str;
use App\Domain\Helpers\StatusService;
use App\Domain\Content\Services\TermService;
use App\Domain\Content\Services\SkillService;
use App\Domain\Content\Services\EquipmentService;

class GenerateCourseService
{    
  /**
   * Holds all of the errors during the generation
  */
  private array $errors = [];

  private CourseService $course_service;
  
  private CourseCategoryService $course_category_service;
  
  private CourseAreaService $course_area_service;
  
  private CourseLessonService $course_lesson_service;
  
  private VideoService $video_service;
  
  private SkillService $skill_service;
  
  private TermService $term_service;
  
  private EquipmentService $equipment_service;
  
  private TrainerService $trainer_service;

  private int $total_course_areas = 6;
  
  private int $total_lessons = 80;
  
  private int $total_videos = 5;
  
  private int $total_skills = 5;
  
  private int $total_terms = 5;
  
  private int $total_equipment = 5;
  
  private int $total_trainers = 6;
  
  private array $course_meta_data = [];
  
  private null|object $created_course;
  
  private array $course_areas_meta_data = [];
  
  private array $created_course_areas = [];
  
  private array $lessons_meta_data = [];

  private array $created_lessons = [];
  
  private array $videos_meta_data = [];

  private array $created_videos = [];
  
  private array $skills_meta_data = [];

  private array $created_skills = [];
  
  private array $terms_meta_data = [];

  private array $created_terms = [];
  
  private array $equipment_meta_data = [];

  private array $created_equipment = [];
  
  public function __construct(
    CourseService $course_service,
    CourseAreaService $course_area_service,
    CourseLessonService $course_lesson_service,
    CourseCategoryService $course_category_service,
    VideoService $video_service, 
    TrainerService $trainer_service,
    SkillService $skill_service,
    TermService $term_service,
    EquipmentService $equipment_service,
  )
  {
    $this->course_category_service  = $course_category_service;
    $this->course_area_service      = $course_area_service;
    $this->course_lesson_service    = $course_lesson_service;
    $this->course_service           = $course_service;
    $this->video_service            = $video_service;
    $this->trainer_service          = $trainer_service;
    $this->skill_service            = $skill_service;
    $this->term_service             = $term_service;
    $this->equipment_service        = $equipment_service;
  }
  
  /**
   * @param int $total
   * @return self
  */
  public function setTotalCourseAreas(int $total): self
  {
    $this->total_course_areas = $total;
    return $this;
  }
  
  /**
   * @param int $total
   * @return self
  */
  public function setTotalTrainers(int $total): self
  {
    $this->total_trainers = $total;
    return $this;
  }
  
  /**
   * @param int $total
   * @return self
  */
  public function setTotalLessons(int $total): self
  {
    $this->total_lessons = $total;
    return $this;
  }
  
  /**
   * @param int $total
   * @return self
  */
  public function setTotalVideos(int $total): self
  {
    $this->total_videos = $total;
    return $this;
  }
  
  /**
   * Gets the generation details as text
   *
   * @return string
  */
  public function getGenerationDetails(): string
  {
    $line_break = "\n";
    return  '1 Course Category, '     .                     $line_break .
            '1 Course, '              .                     $line_break .
            $this->total_course_areas . ' Course Areas, ' . $line_break . 
            $this->total_lessons      . ' lessons, '      . $line_break . 
            $this->total_videos       . ' videos, '       . $line_break .
            $this->total_skills       . ' skills, '       . $line_break .
            $this->total_terms        . ' terms, '        . $line_break .
            $this->total_equipment    . ' equipment, '    . $line_break .
            $this->total_trainers     . ' trainers'
            ;
  }
  
  /**
   * @return bool
  */
  public function hasErrors(): bool
  {
    return count($this->errors);
  }
  
  /**
   * @return string
  */
  public function getErrorsInText(): string
  {
    try {
      $error_message  = '';
      $breaker        = ' | ';
      foreach($this->errors AS $index => $error) {
        $error_message .= $index++ . '. ' . $error . $breaker;
      }

      return $error_message;
    } catch(Exception $ex) {
      return 'Failed to fetch the errors';
    }
  }
  
  /**
   * @return array
  */
  public function getErrors(): array
  {
    return $this->errors;
  }

  public function generate()
  {
    $this->buildTrainersMetaData();
    $this->saveTrainers();

    $this->buildVideosMetaData();
    $this->saveVideos();

    $this->buildSkillsMetaData();
    $this->saveSkills();

    $this->buildTermsMetaData();
    $this->saveTerms();

    $this->buildEquipmentMetaData();
    $this->saveEquipment();

    $this->buildCourseCategoryMetaData();
    $this->saveCourseCategory();

    $this->buildCourseMetaData();
    $this->saveCourse();

    $this->buildCourseAreasMetaData();
    $this->saveCourseAreas();

    $this->buildCourseLessonsMetaData();
    $this->saveCourseLessons();
  }

  private function buildCourseCategoryMetaData()
  {
    $this->course_meta_data = [
      'name'        => ContentFaker::getCourseCategoryName(),
      'description' => ContentFaker::getDescription(),
      'status'      => StatusService::ACTIVE,
      'image'       => $this->getImage(),
    ];
  }

  private function saveCourseCategory()
  {
    $this->created_course = $this->course_category_service->create($this->course_meta_data, 0);
  }

  private function buildCourseMetaData()
  {
    $this->course_meta_data = [
      'category_id' => $this->course_category_service->getRandomCategory()->id,
      'name'        => ContentFaker::getCourseName(),
      'description' => ContentFaker::getDescription(),
      'price'       => $this->getPrice(),
      'discount'    => $this->getDiscount(),
      'view_order'  => $this->course_service->getNextViewOrder(),
      'status'      => StatusService::ACTIVE,
      'image'       => $this->getImage(),
      'trailer'     => $this->getCourseTrailer(),
    ];
  }

  private function saveCourse()
  {
    $this->created_course = $this->course_service->create($this->course_meta_data, 0);
  }

  private function buildCourseAreasMetaData()
  {
    for($index = 0; $index < $this->total_course_areas; $index++) {
      try {
        $this->course_areas_meta_data[] = [
          'course_id'   => $this->created_course->id,
          'trainer_id'  => $this->trainer_service->getRandomTrainer()->id ?? 0,
          'name'        => ContentFaker::getCourseAreaName(),
          'image'       => $this->getImage(),
          'trailer'     => NULL,
          'status'      => StatusService::ACTIVE,
          'price'       => $this->getPrice(),
          'description' => ContentFaker::getDescription(),
          'view_order'  => $this->course_area_service->getNextViewOrder(),
        ];
      } catch(Exception $ex) {
        $this->errors[] = __METHOD__ . ': ' . $ex->__toString();
      }
    }
  }

  private function saveCourseAreas()
  {
    foreach($this->course_areas_meta_data AS $course_area) {
      try {
        $this->created_course_areas[] = $this->course_area_service->create($course_area, 0);
      } catch(Exception $ex) {
        $this->errors[] = __METHOD__ . ': ' . $ex->__toString();
      }
    }
  }

  private function buildVideosMetaData()
  {
    for($index = 0; $index < $this->total_videos; $index++) {
      try {
        $this->videos_meta_data[] = [
          'name'          => ContentFaker::getVideoName() . ' | ' . Str::random(5),
          'description'   => ContentFaker::getDescription(),
          'status'        => StatusService::ACTIVE,
          'video_length'  => 8,
          'file'          => $this->getVideo(),
        ];
      } catch(Exception $ex) {
        $this->errors[] = __METHOD__ . ': ' . $ex->__toString();
      }
    }
  }

  private function saveVideos()
  {
    foreach($this->videos_meta_data AS $video) {
      try {
        $this->created_videos[] = $this->video_service->create($video, 0);
      } catch(Exception $ex) {
        $this->errors[] = __METHOD__ . ': ' . $ex->__toString();
      }
    }
  }

  private function buildSkillsMetaData()
  {
    for($index = 0; $index < $this->total_skills; $index++) {
      try {
        $this->skills_meta_data[] = [
          'name'          => ContentFaker::getSkillName() . ' | ' . Str::random(5),
          'description'   => ContentFaker::getDescription(),
          'status'        => StatusService::ACTIVE,
          'image'         => $this->getImage(),
        ];
      } catch(Exception $ex) {
        $this->errors[] = __METHOD__ . ': ' . $ex->__toString();
      }
    }
  }

  private function saveSkills()
  {
    foreach($this->skills_meta_data AS $skill) {
      try {
        $this->created_skills[] = $this->skill_service->create($skill, 0);
      } catch(Exception $ex) {
        $this->errors[] = __METHOD__ . ': ' . $ex->__toString();
      }
    }
  }

  private function buildTermsMetaData()
  {
    for($index = 0; $index < $this->total_terms; $index++) {
      try {
        $this->terms_meta_data[] = [
          'name'          => ContentFaker::getTermName() . ' | ' . Str::random(5),
          'description'   => ContentFaker::getDescription(),
          'status'        => StatusService::ACTIVE,
        ];
      } catch(Exception $ex) {
        $this->errors[] = __METHOD__ . ': ' . $ex->__toString();
      }
    }
  }

  private function saveTerms()
  {
    foreach($this->terms_meta_data AS $term) {
      try {
        $this->created_terms[] = $this->term_service->create($term, 0);
      } catch(Exception $ex) {
        $this->errors[] = __METHOD__ . ': ' . $ex->__toString();
      }
    }
  }

  private function buildEquipmentMetaData()
  {
    for($index = 0; $index < $this->total_equipment; $index++) {
      try {
        $this->equipment_meta_data[] = [
          'name'          => ContentFaker::getEquipmentName() . ' | ' . Str::random(5),
          'description'   => ContentFaker::getDescription(),
          'status'        => StatusService::ACTIVE,
          'image'         => $this->getImage(),
        ];
      } catch(Exception $ex) {
        $this->errors[] = __METHOD__ . ': ' . $ex->__toString();
      }
    }
  }

  private function saveEquipment()
  {
    foreach($this->equipment_meta_data AS $equipment) {
      try {
        $this->created_equipment[] = $this->equipment_service->create($equipment, 0);
      } catch(Exception $ex) {
        $this->errors[] = __METHOD__ . ': ' . $ex->__toString();
      }
    }
  }

  private function buildTrainersMetaData()
  {
    for($index = 0; $index < $this->total_trainers; $index++) {
      try {
        $this->trainers_meta_data[] = [
          'name'          => ContentFaker::getTrainerName(),
          'title'         => ContentFaker::getTitle(),
          'description'   => ContentFaker::getDescription(),
          'image'         => $this->getImage(),
          'status'        => StatusService::ACTIVE,
        ];
      } catch(Exception $ex) {
        $this->errors[] = __METHOD__ . ': ' . $ex->__toString();
      }
    }
  }

  private function saveTrainers()
  {
    foreach($this->trainers_meta_data AS $trainer) {
      try {
        $this->created_trainers[] = $this->trainer_service->create($trainer, 0);
      } catch(Exception $ex) {
        $this->errors[] = __METHOD__ . ': ' . $ex->__toString();
      }
    }
  }

  private function buildCourseLessonsMetaData()
  {
    for($index = 0; $index < $this->total_lessons; $index++) {
      try {
        $this->lessons_meta_data[] = [
          'course_id'       => $this->created_course->id,
          'course_area_id'  => $this->getCourseAreaId(),
          'name'            => ContentFaker::getCourseAreaName(),
          'image'           => $this->getImage(),
          'video_id'        => $this->video_service->getRandomVideo()->id,
          'status'          => StatusService::ACTIVE,
          'content'         => ContentFaker::getLessonContent(),
          'description'     => ContentFaker::getDescription(),
          'view_order'      => $this->course_lesson_service->getNextViewOrder(),
        ];
      } catch(Exception $ex) {
        $this->errors[] = __METHOD__ . ': ' . $ex->__toString();
      }
    }
  }

  private function saveCourseLessons()
  {
    foreach($this->lessons_meta_data AS $course_area) {
      try {
        $this->created_course_lessons[] = $this->course_lesson_service->create($course_area, 0);
      } catch(Exception $ex) {
        $this->errors[] = __METHOD__ . ': ' . $ex->__toString();
      }
    }
  }
  
  /**
   * @return int
  */
  private function getPrice(): int
  {
    return random_int(500, 2000);
  }
  
  /**
   * @return int
   */
  private function getDiscount(): int
  {
    return random_int(0, 50);
  }
  
  /**
   * @return string
   */
  private function getCourseTrailer(): string
  {
    return 'FakersContent/CourseTrailer.mp4';
  }
  
  /**
   * @return string
   */
  private function getImage(): string
  {
    return 'FakersContent/CourseImage.jpg';
  }
  
  /**
   * @return string
   */
  private function getVideo(): string
  {
    return 'FakersContent/Video.mp4';
  }
  
  /**
   * @return int
  */
  private function getCourseAreaId(): int
  {
    $random_index = random_int(0, count($this->created_course_areas) - 1);
    return $this->created_course_areas[$random_index]->id;
  }
}