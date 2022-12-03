<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Content\Models\Term;
use App\Domain\Helpers\FileService;
use App\Domain\Content\Models\Skill;
use App\Domain\Content\Models\Video;
use App\Domain\Helpers\StatusService;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Interfaces\IContentService;
use App\Domain\Content\Models\CourseLesson;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Content\Models\CourseLessonTerm;
use App\Domain\Helpers\DataManipulationService;
use App\Domain\Content\Models\CourseLessonSkill;
use App\Domain\Content\Models\CourseLessonEquipment;
use App\Domain\Content\Models\CourseLessonTrainingOption;

class CourseLessonService implements IContentService
{
  const FILES_PATH = 'content/lessons';

  private CourseLesson|null $lesson;
 
  private LogService $log_service;

  private CourseAreaService|null $course_area_service;

  private SkillService|null $skill_service;

  private TermService|null $term_service;

  private EquipmentService|null $equipment_service;

  private TrainingOptionService|null $training_option_service;
  
  public function __construct(
    ?CourseAreaService $course_area_service = null,
    ?SkillService $skill_service = null,
    ?TermService $term_service = null,
    ?EquipmentService $equipment_service = null,
    ?TrainingOptionService $training_option_service = null
  )
  {
    $this->course_area_service      = $course_area_service;
    $this->skill_service            = $skill_service;
    $this->term_service             = $term_service;
    $this->equipment_service        = $equipment_service;
    $this->training_option_service  = $training_option_service;
    $this->log_service              = new LogService('courseLessons');
  }
  
  /**
   * @param int $video_id
   * @return CourseLesson
  */
  public function getLessonsWithVideo(int $video_id): CourseLesson
  {
    return CourseLesson::where('video_id', $video_id)
                      ->select('id', 'name', 'status')
                      ->get();
  }
  
  /**
   * @param int $lesson_id
   * @return Video
  */
  public function getVideoByLessonId(int $lesson_id): Video
  {
    $lesson = CourseLesson::where('id', $lesson_id)
                      ->with('video')
                      ->select('id', 'video_id')
                      ->first();

    return $lesson->video;
  }
  
  /**
   * @param int $course_area_id
   * @return Collection|null
  */
  public function getLessonsOfCourseArea(int $course_area_id): ?Collection
  {
    return CourseLesson::where('course_area_id', $course_area_id)
                      ->select('id', 'name', 'status')
                      ->get();
  }
  
  /**
   * @param array|int $lessons_ids
   * @return Collection|null
  */
  public function getLessonsByIds($lessons_ids): ?Collection
  {
    $lessons_ids = DataManipulationService::intToArray($lessons_ids);

    return CourseLesson::whereIn('id', $lessons_ids)
                      ->select('id', 'course_id', 'course_area_id', 'name', 'status', 'image')
                      ->get();
  }
  
  /**
   * @param array|int $courses_id
   * @return Collection|null
  */
  public function getLessonsByCoursesId($courses_id): ?Collection
  {
    $lessons_ids = DataManipulationService::intToArray($courses_id);

    return CourseLesson::whereIn('course_id', $lessons_ids)
                      ->select('id')
                      ->get();
  }

  /**
   * @param int $course_id
   * @return Collection
  */
  public function getLessonsDurationByCourseId(int $course_id): Collection
  {
    return CourseLesson::where('course_id', $course_id)
                        ->join('videos', 'videos.id', 'course_lessons.video_id')
                        ->select('course_lessons.id', 'video_length')
                        ->get();
  }

  /**
   * @param int $lesson_id
   * @return bool
  */
  public function getLessonCourseId(int $lesson_id): bool
  {
    return CourseLesson::where('id', $lesson_id)->value('course_id');
  }

  /**
   * @param int $video_id
   * @return bool
  */
  public function isVideoInUsed(int $video_id): bool
  {
    return CourseLesson::where('video_id', $video_id)->exists();
  }

  /**
   * @param int $skill_id
   * @return bool
  */
  public function isSkillInUsed(int $skill_id): bool
  {
    return CourseLessonSkill::where('skill_id', $skill_id)->exists();
  }

  /**
   * @param int $equipment_id
   * @return bool
  */
  public function isEquipmentInUsed(int $equipment_id): bool
  {
    return CourseLessonEquipment::where('equipment_id', $equipment_id)->exists();
  }

  /**
   * @param int $term_id
   * @return bool
  */
  public function isTermInUsed(int $term_id): bool
  {
    return CourseLessonTerm::where('term_id', $term_id)->exists();
  }

  /**
   * @param int $training_option_id
   * @return bool
  */
  public function isTrainingOptionInUsed(int $training_option_id): bool
  {
    return CourseLessonTrainingOption::where('training_option_id', $training_option_id)->exists();
  }

  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return $this->baseQueryBuilder()
              ->with('skills', 'equipment', 'terms', 'trainingOptions')
              ->orderBy('course_lessons.id', 'desc')
              ->get();
  }

  /**
   * @return Collection
   * 
   * @param int $lessons
   * @param int $status
  */
  public function getRandomActiveLessons(int $lessons = 4, int $status = 1): Collection
  {
    return CourseLesson::query()
              ->limit($lessons)
              ->inRandomOrder()
              ->where('status', $status)
              ->select('name', 'description', 'image')
              ->get();
  }
  
  /**
   * @param array $lessons_ids
   * @param int $course_area_id
   * @param int $course_id
   * @return bool
  */
  public function assignCourseArea(array $lessons_ids, int $course_area_id, int $course_id): bool
  {
    $lessons_found = CourseLesson::whereIn('id', $lessons_ids)->count();
    if($lessons_found !== count($lessons_ids)) {
      return false;
    }

    CourseLesson::whereIn('id', $lessons_ids)->update([
      'course_id'       => $course_id,
      'course_area_id'  => $course_area_id
    ]);
    
    return true;
  }
  
  /**
   * @param array $lessons_ids
   * @return bool
  */
  public function unAssignLessons(array $lessons_ids): bool
  {
    $lessons_found = CourseLesson::whereIn('id', $lessons_ids)->count();
    if($lessons_found !== count($lessons_ids)) {
      return false;
    }

    CourseLesson::whereIn('id', $lessons_ids)->update([
      'course_id'       => null,
      'course_area_id'  => null
    ]);
    
    return true;
  }
        
  /**
   * Gets the next view order of the course area
   *
   * @param int $course_area_id
   * @return int
  */
  public function getLessonViewOrder(int $course_area_id): int
  {
    $last_view_order = CourseLesson::where('course_area_id', $course_area_id)->orderBy('view_order', 'desc')->value('view_order');
    return $last_view_order ? ++$last_view_order : 1; 
  }
        
  /**
   * Checks the latest view order and returns the next one
   *
   * @return int
  */
  public function getNextViewOrder(): int
  {
    $last_view_order = CourseLesson::orderBy('view_order', 'desc')->value('view_order');
    return $last_view_order ? $last_view_order++ : 1; 
  }
  
  /**
   * Fully deletes all of the content
   *
   * @return void
  */
  public function truncate()
  {
    $lessons_ids = CourseLesson::withTrashed()->pluck('id');
    foreach($lessons_ids AS $lesson_id) {
      $this->forceDelete($lesson_id, 0);
    }
    CourseLesson::truncate();
  }

  /**
   * @param array $data
   * @param int $created_by
   * @return CourseLesson|null
  */
  public function create(array $data, int $created_by): ?CourseLesson
  {
    $lesson                   = new CourseLesson;
    $lesson->image            = FileService::create($data['image'], self::FILES_PATH);
    $lesson->course_id        = $this->course_area_service->getById($data['course_area_id'])->course_id;
    $lesson->view_order       = $this->getLessonViewOrder($data['course_area_id']);
    $lesson->course_area_id   = $data['course_area_id'];
    $lesson->video_id         = $data['video_id'];
    $lesson->name             = $data['name'];
    $lesson->content          = $data['content'];
    $lesson->description      = $data['description'];
    $lesson->status           = $data['status']           ?? StatusService::PENDING;

    try {
      $lesson->save();
      $this->assignTrainingOptions($lesson->id, $data['training_options'], $created_by);
      $this->assignSkills($lesson->id, $data['skills'], $created_by);
      $this->assignTerms($lesson->id, $data['terms'], $created_by);
      $this->assignEquipment($lesson->id, $data['equipment'], $created_by);
    } catch(Exception $ex) {
      FileService::delete($lesson->image);
      throw $ex;
    }

    $this->log_service->info('Lesson ' . $lesson->id . ' has been created: ' . json_encode($lesson));

    return $this->baseQueryBuilder()
          ->where('course_lessons.id', $lesson->id)
          ->first();
  }

  /**
   * @param array $data
   * @param int $updated_by
   * @return CourseLesson|null
  */
  public function update(array $data, int $updated_by): ?CourseLesson
  {
    if(!$lesson = CourseLesson::find($data['id'])) {
      throw new Exception('Course Lesson not found');
    };

    if(!empty($data['image'])) {
      FileService::delete($lesson->image);
      $lesson->image        = FileService::create($data['image'], self::FILES_PATH);
    }

    $lesson->course_id      = $this->course_area_service->getById($data['course_area_id'])->course_id;
    $lesson->course_area_id = $data['course_area_id'];
    $lesson->name           = $data['name'];
    $lesson->content        = $data['content'];
    $lesson->description    = $data['description'];
    $lesson->status         = $data['status'];
    
    $lesson->save();

    $this->assignTrainingOptions($lesson->id, $data['training_options'], $updated_by);
    $this->assignSkills($lesson->id, $data['skills'], $updated_by);
    $this->assignTerms($lesson->id, $data['terms'], $updated_by);
    $this->assignEquipment($lesson->id, $data['equipment'], $updated_by);

    $this->log_service->info('Lesson ' . $lesson->id . ' has been updated: ' . json_encode($lesson));

    return $lesson;
  }
  
  /**
   * @param int $lesson_id
   * @param array $equipment
   * @param int $created_by
   * @return void
  */
  public function assignEquipment(int $lesson_id, array $equipment, int $created_by)
  {
    $this->deleteLessonEquipment($lesson_id);

    if(!count($equipment)) {
      return;
    }

    if(!$this->equipment_service->equipmentExist($equipment)) {
      throw new Exception('One or more equipment were not found, equipment: ' . json_encode($equipment));
    }

    $this->addLessonEquipment($lesson_id, $equipment, $created_by);
  }
  
  /**
   * @param int $lesson_id
   * @param array $terms
   * @param int $created_by
   * @return void
  */
  public function assignTerms(int $lesson_id, array $terms, int $created_by)
  {
    $this->deleteLessonTerms($lesson_id);

    if(!count($terms)) {
      return;
    }

    if(!$this->term_service->termsExist($terms)) {
      throw new Exception('One or more terms were not found, terms: ' . json_encode($terms));
    }
    
    $this->addLessonTerms($lesson_id, $terms, $created_by);
  }
  
  /**
   * @param int $lesson_id
   * @param array $training_options
   * @param int $created_by
   * @return void
  */
  public function assignTrainingOptions(int $lesson_id, array $training_options, int $created_by)
  {
    $this->deleteLessonTrainingOptions($lesson_id);

    if(!count($training_options)) {
      return;
    }

    if(!$this->training_option_service->trainingOptionsExist(collect($training_options)->pluck('id')->toArray())) {
      throw new Exception('One or more Training Options were not found, Training Options: ' . json_encode($training_options));
    }

    $this->addLessonTrainingOptions($lesson_id, $training_options, $created_by);
  }
  
  /**
   * @param int $lesson_id
   * @param array $skills
   * @param int $created_by
   * @return void
  */
  public function assignSkills(int $lesson_id, array $skills, int $created_by)
  {
    $this->deleteLessonSkills($lesson_id);

    if(!count($skills)) {
      return;
    }

    if(!$this->skill_service->skillsExist($skills)) {
      throw new Exception('One or more skills were not found, skills: ' . json_encode($skills));
    }

    $this->addLessonSkills($lesson_id, $skills, $created_by);
  }
  
  /**
   * update the view order of the content
   *
   * @param array $lessons
   * @return void
  */
  public function updateOrder(array $lessons)
  {
    foreach($lessons AS $lesson) {
      CourseLesson::where('id', $lesson['id'])->update(['view_order' => $lesson['view_order']]);
    }
  }
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $lesson_id) {
      $this->delete($lesson_id, $deleted_by);
    }
  } 
  
  /**
   * Soft delete the item 
   * @param int $lesson_id
   * @param int $deleted_by
   * @return bool
  */
  public function delete(int $lesson_id, int $deleted_by): bool
  {
    $this->validateIfCanDelete($lesson_id);

    $result = $this->lesson->delete();
    $this->log_service->info('Lesson ' . $lesson_id . ' has been deleted');
    return $result;
  }
  
  /**
   * @param int $lesson_id
   * @param int $deleted_by
   * @return bool
  */
  public function forceDelete(int $lesson_id, int $deleted_by): bool
  {
    $this->validateIfCanDelete($lesson_id);

    FileService::delete($this->lesson->image);
    $result = $this->lesson->forceDelete();
    $this->log_service->info('Lesson ' . $lesson_id . ' has been forced deleted');
    return $result;
  }
    
  /**
   * @param int $course_area_id
   * @return bool
  */
  public function isCourseAreaInUsed(int $course_area_id): bool
  {
    return CourseLesson::where('course_area_id', $course_area_id)->exists();
  }
    
  /**
   * @return void
  */
  public function truncateAllLessonAssignedContent()
  {
    $this->truncateLessonSkills();
    $this->truncateLessonTerms();
    $this->truncateLessonEquipment();
  }
  
  /**
   * Throws an error if failed the validation and cannot delete
   * If it can be deleted, stores the content in the class state
   * @param int $lesson_id
   * @return void
  */
  private function validateIfCanDelete(int $lesson_id)
  {
    if(!$lesson = CourseLesson::find($lesson_id)) {
      throw new Exception('Course Lesson not found');
    }

    $this->lesson = $lesson;
  }
  
  /**
   * @return void
  */
  private function truncateLessonSkills()
  {
    CourseLessonSkill::truncate();
  }
  
  /**
   * @return void
  */
  private function truncateLessonTerms()
  {
    CourseLessonTerm::truncate();
  }
  
  /**
   * @return void
  */
  private function truncateLessonEquipment()
  {
    CourseLessonEquipment::truncate();
  }
  
  /**
   * @param int $lesson_id
   * @return void
  */
  private function deleteLessonEquipment(int $lesson_id)
  {
    CourseLessonEquipment::where('course_lesson_id', $lesson_id)->delete();
  }
  
  /**
   * @param int $lesson_id
   * @param array $equipment
   * @param int $created_by
   * @return void
  */
  private function addLessonEquipment(int $lesson_id, array $equipment, int $created_by)
  {
    $lesson_equipment = [];
    foreach($equipment AS $equipment) {
      $lesson_equipment[] = [
        'equipment_id'      => $equipment,
        'course_lesson_id'  => $lesson_id,
        'created_at'        => now(),
        'created_by'        => $created_by,
      ];
    }

    CourseLessonEquipment::insert($lesson_equipment);
  }
  
  /**
   * @param int $lesson_id
   * @return void
  */
  private function deleteLessonTerms(int $lesson_id)
  {
    CourseLessonTerm::where('course_lesson_id', $lesson_id)->delete();
  }
  
  /**
   * @param int $lesson_id
   * @param array $terms
   * @param int $created_by
   * @return void
  */
  private function addLessonTerms(int $lesson_id, array $terms, int $created_by)
  {
    $lesson_terms = [];
    foreach($terms AS $term) {
      $lesson_terms[] = [
        'term_id'           => $term,
        'course_lesson_id'  => $lesson_id,
        'created_at'        => now(),
        'created_by'        => $created_by,
      ];
    }

    CourseLessonTerm::insert($lesson_terms);
  }
  
  /**
   * @param int $lesson_id
   * @return void
  */
  private function deleteLessonTrainingOptions(int $lesson_id)
  {
    CourseLessonTrainingOption::where('course_lesson_id', $lesson_id)->delete();
  }
  
  /**
   * @param int $lesson_id
   * @param array $training_options
   * @param int $created_by
   * @return void
  */
  private function addLessonTrainingOptions(int $lesson_id, array $training_options, int $created_by)
  {
    $lesson_training_options = [];
    foreach($training_options AS $training_option) {
      $lesson_training_options[] = [
        'course_lesson_id'    => $lesson_id,
        'training_option_id'  => $training_option['id'],
        'value'               => $training_option['value'],
        'created_at'          => now(),
        'created_by'          => $created_by,
      ];
    }

    CourseLessonTrainingOption::insert($lesson_training_options);
  }
  
  /**
   * @param int $lesson_id
   * @return void
  */
  private function deleteLessonSkills(int $lesson_id)
  {
    CourseLessonSkill::where('course_lesson_id', $lesson_id)->delete();
  }
  
  /**
   * @param int $lesson_id
   * @param array $skills
   * @param int $created_by
   * @return void
  */
  private function addLessonSkills(int $lesson_id, array $skills, int $created_by)
  {
    $lesson_skills = [];
    foreach($skills AS $skill) {
      $lesson_skills[] = [
        'skill_id'          => $skill,
        'course_lesson_id'  => $lesson_id,
        'type'              => Skill::LEARNS_TYPE,
        'created_at'        => now(),
        'created_by'        => $created_by,
      ];
    }

    CourseLessonSkill::insert($lesson_skills);
  }

  /**
   * Build base query
   *
   * @return Builder
  */   
  private function baseQueryBuilder(): Builder
  {
    return CourseLesson::query()
            ->leftJoin('course_areas', 'course_areas.id', 'course_lessons.course_area_id')
            ->leftJoin('courses', 'courses.id', 'course_lessons.course_id')
            ->leftJoin('course_categories', 'course_categories.id', 'courses.category_id')
            ->join('videos', 'videos.id', 'course_lessons.video_id')
            ->select(
              'course_lessons.id',
              'course_lessons.course_id',
              'course_lessons.video_id',
              'course_lessons.course_area_id',
              'course_lessons.name',
              'course_lessons.content',
              'course_lessons.description',
              'course_lessons.status',
              'course_lessons.view_order',
              'course_lessons.created_at',
              'courses.name AS course_name',
              'course_areas.name AS course_area_name',
              'course_categories.name AS course_category_name',
              'videos.video_path',
              'videos.video_length',
            );
  }
}