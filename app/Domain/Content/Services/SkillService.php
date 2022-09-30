<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Content\Models\Skill;
use App\Domain\Helpers\StatusService;
use App\Domain\Interfaces\IContentService;
use Illuminate\Database\Eloquent\Collection;

class SkillService implements IContentService
{
  const FILES_PATH = 'content/skills';

  private Skill|null $skill;

  private LogService $log_service;

  private CourseLessonService|null $course_lesson_service;
  
  public function __construct(CourseLessonService $course_lesson_service = null)
  {
    $this->course_lesson_service = $course_lesson_service;
    $this->log_service = new LogService('skills');
  }
    
  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return Skill::select(
                'id',
                'name',
                'status',
                'description',
                'image',
                'created_at',
              )
              ->orderBy('id', 'desc')
              ->get();
  }
    
  /**
   * @return null|Skill
  */
  public function getRandomSkill(): ?Skill
  {
    return Skill::inRandomOrder()->first();
  }
      
  /**
   * Fully deletes all of the content
   *
   * @return void
  */
  public function truncate()
  {
    $skills_ids = Skill::withTrashed()->pluck('id');
    foreach($skills_ids AS $skill_id) {
      $this->forceDelete($skill_id, 0);
    }
    Skill::truncate();
  }

  /**
   * @param array $data
   * @param int $created_by
   * @return Skill
  */
  public function create(array $data, int $created_by): ?Skill
  {
    $skill                = new Skill;
    $skill->name          = $data['name'];
    $skill->description   = $data['description'];
    $skill->status        = StatusService::ACTIVE;
    $skill->image         = FileService::create($data['file'], self::FILES_PATH);
    $skill->created_by    = $created_by;
    $skill->status        = $data['status'] ?? StatusService::PENDING;

    $skill->save();

    $this->log_service->info('Skill has been created: ' . json_encode($skill));

    return $skill;
  }
    
  /**
   * @param array $data
   * @param int $updated_by
   * @return Skill
  */
  public function update(array $data, int $updated_by): ?Skill
  {
    if(!$skill = Skill::find($data['id'])) {
      throw new Exception('Skill not found');
    }

    $skill->name         = $data['name'];
    $skill->description  = $data['description'];
    $skill->status       = $data['status'];

    if(!empty($data['file'])) {
      FileService::delete($skill->image);
      $skill->image = FileService::create($data['file'], self::FILES_PATH);
    }

    $skill->save();
    
    $this->log_service->info('Skill has been updated: ' . json_encode($skill));

    return $skill;
  }
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $skill_id) {
      if($error = $this->delete($skill_id, $deleted_by)) {
        return $error;
      }
    }
  } 
  
  /**
   * Soft delete the item
   * @param int $skill_id
   * @param int $deleted_by
   * @return bool
  */
  public function delete(int $skill_id, int $deleted_by): bool
  {
    $this->validateIfCanDelete($skill_id);

    $result = $this->skill->delete();
    $this->log_service->info('Skill ' . $skill_id . ' has been deleted');
    return $result;
  }
  
  /**
   * @param int $skill_id
   * @param int $deleted_by
   * @return bool
  */
  public function forceDelete(int $skill_id, int $deleted_by): bool
  {
    $this->validateIfCanDelete($skill_id);

    FileService::delete($this->skill->image);
    $result = $this->skill->forceDelete();
    $this->log_service->info('Skill ' . $skill_id . ' has been forced deleted');
    return $result;
  }
  
  /**
   * Throws an error if failed the validation and cannot delete
   * If it can be deleted, stores the content in the class state
   * @param int $skill_id
   * @return void
  */
  private function validateIfCanDelete(int $skill_id)
  {
    if(!$skill = Skill::find($skill_id)) {
      throw new Exception('Skill not found');
    }

    if($this->isSkillInUsed($skill_id)) {
      throw new Exception('Cannot delete skill that is being used');
    }

    $this->skill = $skill;
  }

  /**
   * @param int $skill_id
   * @return bool
  */
  private function isSkillInUsed($skill_id): bool
  {
    return $this->course_lesson_service->isSkillInUsed($skill_id);
  }
}