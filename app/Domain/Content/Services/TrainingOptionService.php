<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Content\Models\TrainingOption;
use App\Domain\Helpers\StatusService;
use App\Domain\Interfaces\IContentService;
use Illuminate\Database\Eloquent\Collection;

class TrainingOptionService implements IContentService
{
  private TrainingOption|null $training_option;

  private LogService $log_service;

  private CourseLessonService|null $course_lesson_service;
  
  public function __construct(CourseLessonService $course_lesson_service = null)
  {
    $this->course_lesson_service = $course_lesson_service;
    $this->log_service = new LogService('trainingOptions');
  }
    
  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return TrainingOption::select(
                'id',
                'name',
                'created_at',
              )
              ->orderBy('id', 'desc')
              ->get();
  }
    
  /**
   * @return null|TrainingOption
  */
  public function getRandomTrainingOption(): ?TrainingOption
  {
    return TrainingOption::inRandomOrder()->first();
  }
          
  /**
   * @param int $limit the amount of training_options to fetch
   * @return Collection
  */
  public function getRandomTrainingOptions(int $limit = 1): Collection
  {
    return TrainingOption::inRandomOrder()->limit($limit)->get();
  }

  /**
   * Fully deletes all of the content
   *
   * @return void
  */
  public function truncate()
  {
    $training_options_ids = TrainingOption::withTrashed()->pluck('id');
    foreach($training_options_ids AS $training_option_id) {
      $this->forceDelete($training_option_id, 0);
    }
    TrainingOption::truncate();
  }

  /**
   * @param array $data
   * @param int $created_by
   * @return TrainingOption
  */
  public function create(array $data, int $created_by): ?TrainingOption
  {
    $training_option                = new TrainingOption;
    $training_option->name          = $data['name'];
    $training_option->created_by    = $created_by;

    $training_option->save();

    $this->log_service->info('Training Option has been created: ' . json_encode($training_option));

    return $training_option;
  }
    
  /**
   * @param array $data
   * @param int $updated_by
   * @return TrainingOption
  */
  public function update(array $data, int $updated_by): ?TrainingOption
  {
    if(!$training_option = TrainingOption::find($data['id'])) {
      throw new Exception('TrainingOption not found');
    }

    $training_option->name = $data['name'];
    $training_option->save();
    
    $this->log_service->info('Training Option has been updated: ' . json_encode($training_option));

    return $training_option;
  }
    
  /**
   * @param array $ids
   * @return true
  */
  public function trainingOptionsExist(array $ids): bool
  {
    return TrainingOption::whereIn('id', $ids)->exists();    
  } 
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $training_option_id) {
      $this->delete($training_option_id, $deleted_by);
    }
  } 
  
  /**
   * Soft delete the item
   * @param int $training_option_id
   * @param int $deleted_by
   * @return bool
  */
  public function delete(int $training_option_id, int $deleted_by): bool
  {
    $this->validateIfCanDelete($training_option_id);

    $result = $this->training_option->delete();
    $this->log_service->info('Training Option ' . $training_option_id . ' has been deleted');
    return $result;
  }
  
  /**
   * @param int $training_option_id
   * @param int $deleted_by
   * @return bool
  */
  public function forceDelete(int $training_option_id, int $deleted_by): bool
  {
    $this->validateIfCanDelete($training_option_id);

    $result = $this->training_option->forceDelete();
    $this->log_service->info('Training Option ' . $training_option_id . ' has been forced deleted');
    return $result;
  }
  
  /**
   * Throws an error if failed the validation and cannot delete
   * If it can be deleted, stores the content in the class state
   * @param int $training_option_id
   * @return void
  */
  private function validateIfCanDelete(int $training_option_id)
  {
    if(!$training_option = TrainingOption::withTrashed()->find($training_option_id)) {
      throw new Exception('Training Option not found');
    }

    if($this->isTrainingOptionInUsed($training_option_id)) {
      throw new Exception('Cannot delete Training Option that is being used');
    }

    $this->training_option = $training_option;
  }

  /**
   * @param int $training_option_id
   * @return bool
  */
  private function isTrainingOptionInUsed($training_option_id): bool
  {
    return $this->course_lesson_service->isTrainingOptionInUsed($training_option_id);
  }
}