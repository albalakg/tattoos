<?php

namespace App\Domain\Content\Services;

use Exception;
use Illuminate\Support\Str;
use App\Domain\Content\Models\Course;
use App\Domain\Helpers\StatusService;
use Illuminate\Support\Facades\Storage;

class GenerateCourseService
{    
  /**
   * Holds all of the errors during the generation
   *
   * @var array
  */
  private $errors = [];

  /**
   * @var CourseService
  */
  private $course_service;
  
  /**
   * @var CourseCategoryService
  */
  private $course_category_service;
  
  /**
   * @var CourseAreaService
  */
  private $course_area_service;
  
  /**
   * @var CourseLessonService
  */
  private $course_lesson_service;
  
  /**
   * @var VideoService
  */
  private $video_service;
  
  /**
   * @var TrainerService
  */
  private $trainer_service;

  /**
   * @var int
  */
  private $total_course_areas = 6;
  
  /**
   * @var int
  */
  private $total_lessons = 80;
  
  /**
   * @var int
  */
  private $total_videos = 5;
  
  /**
   * @var int
  */
  private $total_trainers = 6;
  
  /**
   * @var array
  */
  private $course_meta_data = [];
  
  /**
   * @var null|Course
  */
  private $created_course;
  
  /**
   * @var array
  */
  private $course_areas_meta_data = [];
  
  /**
   * @var array
  */
  private $created_course_areas = [];
  
  /**
   * @var array
  */
  private $lessons_meta_data = [];

  /**
   * @var array
  */
  private $created_lessons = [];
  
  /**
   * @var array
  */
  private $videos_meta_data = [];

  /**
   * @var array
  */
  private $created_videos = [];
  
  public function __construct(CourseService $course_service, CourseAreaService $course_area_service, CourseLessonService $course_lesson_service, CourseCategoryService $course_category_service, VideoService $video_service, TrainerService $trainer_service)
  {
    $this->course_category_service  = $course_category_service;
    $this->course_area_service      = $course_area_service;
    $this->course_lesson_service    = $course_lesson_service;
    $this->course_service           = $course_service;
    $this->video_service            = $video_service;
    $this->trainer_service          = $trainer_service;
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
    return  '1 Course Category, '     . 
            '1 Course, '              . 
            $this->total_course_areas . ' Course Areas, ' . 
            $this->total_lessons      . ' lessons, ' . 
            $this->total_videos       . ' videos, ' .
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
      'image'       => $this->getCourseImage(),
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
      'image'       => $this->getCourseImage(),
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
          'image'       => $this->getCourseImage(),
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

  private function buildTrainersMetaData()
  {
    for($index = 0; $index < $this->total_trainers; $index++) {
      try {
        $this->trainers_meta_data[] = [
          'name'          => ContentFaker::getTrainerName(),
          'title'         => ContentFaker::getTitle(),
          'description'   => ContentFaker::getDescription(),
          'image'         => $this->getCourseImage(),
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
          'image'           => $this->getCourseImage(),
          'video_id'        => $this->video_service->getRandomVideo()->id,
          'status'          => StatusService::ACTIVE,
          'content'         => $this->getLessonContent(),
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
   * @return string
   */
  private function getLessonContent(): string
  {
    return Str::random(random_int(10, 1000));
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
  private function getCourseImage(): string
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