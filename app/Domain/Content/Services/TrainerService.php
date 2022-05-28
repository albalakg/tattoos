<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Helpers\StatusService;
use App\Domain\Content\Models\Trainer;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Interfaces\IContentService;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Content\Services\CourseAreaService;

class TrainerService implements IContentService
{
  const FILES_PATH = 'content/trainers';

  /**
   * @var LogService
  */
  private $log_service;
  
  /**
   * @var CourseAreaService|null
  */
  private $course_area_service;
    
  /**
   * Contain the error data
   *
   * @var mixed
  */
  public $error_data;
  
  public function __construct(CourseAreaService $course_area_service = null)
  {
    $this->course_area_service = $course_area_service;
    $this->log_service = new LogService('trainers');
  }
  
  /**
   * @param int $trainer_id
   * @return Trainer|null
  */
  public function getById(int $trainer_id): ?Trainer
  {
    return Trainer::find($trainer_id);
  }

  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return $this->baseQueryBuilder()
              ->orderBy('id', 'desc')
              ->get();
  }

  /**
   * @return Collection
  */
  public function getTrainersForApp(): Collection
  {
    return $this->baseQueryBuilder()
              ->where('status', StatusService::ACTIVE)
              ->orderBy('id', 'desc')
              ->select('name', 'description', 'image')
              ->get();
  }
  
  /**
   * @return null|Trainer
  */
  public function getRandomTrainer(): ?Trainer
  {
    return Trainer::inRandomOrder()->first();
  }
      
  /**
   * Fully deletes all of the content
   *
   * @return void
  */
  public function truncate()
  {
    $trainers_ids = Trainer::withTrashed()->pluck('id');
    foreach($trainers_ids AS $trainer_id) {
      $this->forceDelete($trainer_id, 0);
    }
  }

  /**
   * @param array $data
   * @param int $created_by
   * @return Trainer|null 
  */
  public function create(array $data, int $created_by): ?Trainer
  {
    $trainer               = new Trainer;
    $trainer->name         = $data['name'];
    $trainer->title        = $data['title'];
    $trainer->description  = $data['description'];
    $trainer->status       = StatusService::PENDING;
    $trainer->image        = FileService::create($data['image'], self::FILES_PATH);
    $trainer->created_by   = $created_by;
    $trainer->save();

    $this->log_service->info('Trainer has been created: ' . json_encode($trainer));

    return $trainer;
  }

  /**
   * @param array $data
   * @param int $updated_by
   * @return Trainer|null
  */
  public function update(array $data, int $updated_by): ?Trainer
  {
    if(!$trainer = Trainer::find($data['id'])) {
      throw new Exception('Trainer not found');
    };

    $trainer->name         = $data['name'];
    $trainer->title        = $data['title'];
    $trainer->description  = $data['description'];
    $trainer->status       = $data['status'];
    
    if(!empty($data['image'])) {
      FileService::delete($data['image']);
      $trainer->image      = FileService::create($data['image'], self::FILES_PATH);
    }
    
    $trainer->save();
    
    $this->log_service->info('Trainer has been updated: ' . json_encode($trainer));

    return $trainer;
  }
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $trainer_id) {
      if($error = $this->delete($trainer_id, $deleted_by)) {
        return $error;
      }
    }
  } 
  
  /**
   * Soft delete the item 
   * @param int $trainer_id
   * @param int $deleted_by
   * @return bool
  */
  public function delete(int $trainer_id, int $deleted_by): bool
  {
    if(!$trainer = $this->canDelete($trainer_id)) {
      return false;
    }
    
    $result = $trainer->delete();
    $this->log_service->info('Trainer ' . $trainer_id . ' has been deleted');
    return $result;
  }
  
  /**
   * @param int $trainer_id
   * @param int $deleted_by
   * @return bool
  */
  public function forceDelete(int $trainer_id, int $deleted_by): bool
  {
    if(!$trainer = $this->canDelete($trainer_id)) {
      return false;
    }

    FileService::delete($trainer->image);

    $result = $trainer->forceDelete();
    $this->log_service->info('Trainer ' . $trainer_id . ' has been forced deleted');
    return $result;
  }
  
  /**
   * @param int $trainer_id
   * @return Trainer
  */
  private function canDelete(int $trainer_id): Trainer
  {
    if(!$trainer = Trainer::where('id', $trainer_id)->withTrashed()->first()) {
      throw new Exception('Trainer not found');
    }

    if($this->isTrainerInUsed($trainer_id)) {
      throw new Exception('Cannot delete Trainer that is being used');
    }

    return $trainer;
  }
  
  /**
   * @param int $trainer_id
   * @return bool
  */
  private function isTrainerInUsed(int $trainer_id): bool
  {
    return $this->course_area_service->isTrainerInUsed($trainer_id);
  }
  
  /**
   * Build base query
   *
   * @return Builder
  */   
  private function baseQueryBuilder(): Builder
  {
    return Trainer::query();
  }
}