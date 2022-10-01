<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use App\Domain\Content\Models\Equipment;
use App\Domain\Helpers\StatusService;
use App\Domain\Interfaces\IContentService;
use Illuminate\Database\Eloquent\Collection;

class EquipmentService implements IContentService
{
  const FILES_PATH = 'content/equipment';

  private Equipment|null $equipment;

  private LogService $log_service;

  private CourseLessonService|null $course_lesson_service;
  
  public function __construct(CourseLessonService $course_lesson_service = null)
  {
    $this->course_lesson_service = $course_lesson_service;
    $this->log_service = new LogService('equipment');
  }
    
  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return Equipment::select(
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
   * @return null|Equipment
  */
  public function getRandomEquipment(): ?Equipment
  {
    return Equipment::inRandomOrder()->first();
  }
      
  /**
   * Fully deletes all of the content
   *
   * @return void
  */
  public function truncate()
  {
    $equipment_ids = Equipment::withTrashed()->pluck('id');
    foreach($equipment_ids AS $equipment_id) {
      $this->forceDelete($equipment_id, 0);
    }
    Equipment::truncate();
  }

  /**
   * @param array $data
   * @param int $created_by
   * @return Equipment
  */
  public function create(array $data, int $created_by): ?Equipment
  {
    $equipment                = new Equipment;
    $equipment->name          = $data['name'];
    $equipment->description   = $data['description'];
    $equipment->status        = StatusService::ACTIVE;
    $equipment->image         = FileService::create($data['file'], self::FILES_PATH);
    $equipment->created_by    = $created_by;
    $equipment->status        = $data['status'] ?? StatusService::PENDING;

    $equipment->save();

    $this->log_service->info('Equipment has been created: ' . json_encode($equipment));

    return $equipment;
  }
    
  /**
   * @param array $data
   * @param int $updated_by
   * @return Equipment
  */
  public function update(array $data, int $updated_by): ?Equipment
  {
    if(!$equipment = Equipment::find($data['id'])) {
      throw new Exception('Equipment not found');
    }

    $equipment->name         = $data['name'];
    $equipment->description  = $data['description'];
    $equipment->status       = $data['status'];

    if(!empty($data['file'])) {
      FileService::delete($equipment->image);
      $equipment->image = FileService::create($data['file'], self::FILES_PATH);
    }

    $equipment->save();
    
    $this->log_service->info('Equipment has been updated: ' . json_encode($equipment));

    return $equipment;
  }
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $equipment_id) {
      if($error = $this->delete($equipment_id, $deleted_by)) {
        return $error;
      }
    }
  } 
  
  /**
   * Soft delete the item
   * @param int $equipment_id
   * @param int $deleted_by
   * @return bool
  */
  public function delete(int $equipment_id, int $deleted_by): bool
  {
    $this->validateIfCanDelete($equipment_id);

    $result = $this->equipment->delete();
    $this->log_service->info('Equipment ' . $equipment_id . ' has been deleted');
    return $result;
  }
  
  /**
   * @param int $equipment_id
   * @param int $deleted_by
   * @return bool
  */
  public function forceDelete(int $equipment_id, int $deleted_by): bool
  {
    $this->validateIfCanDelete($equipment_id);

    FileService::delete($this->equipment->image);
    $result = $this->equipment->forceDelete();
    $this->log_service->info('Equipment ' . $equipment_id . ' has been forced deleted');
    return $result;
  }
  
  /**
   * Throws an error if failed the validation and cannot delete
   * If it can be deleted, stores the content in the class state
   * @param int $equipment_id
   * @return void
  */
  private function validateIfCanDelete(int $equipment_id)
  {
    if(!$equipment = Equipment::find($equipment_id)) {
      throw new Exception('Equipment not found');
    }

    if($this->isEquipmentInUsed($equipment_id)) {
      throw new Exception('Cannot delete equipment that is being used');
    }

    $this->equipment = $equipment;
  }

  /**
   * @param int $equipment_id
   * @return bool
  */
  private function isEquipmentInUsed($equipment_id): bool
  {
    return $this->course_lesson_service->isEquipmentInUsed($equipment_id);
  }
}