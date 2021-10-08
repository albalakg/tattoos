<?php
namespace App\Domain\Support\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\StatusService;
use App\Domain\Support\Models\SupportCategory;
use Illuminate\Database\Eloquent\Collection;

class SupportCategoryService
{
  /**
   * @var LogService
  */
  private $log_service;

  public function __construct()
  {
    $this->log_service = new LogService('support');
  }
  
  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return SupportCategory::orderBy('created_at', 'desc')
                ->get();
  }
  
  /**
   * @param array $data
   * @param int $created_by
   * @return SupportCategory
  */
  public function create(array $data, int $created_by): SupportCategory
  {
    $support_category               = new SupportCategory();
    $support_category->name         = $data['name'];
    $support_category->description  = $data['description'];
    $support_category->status       = StatusService::ACTIVE;
    $support_category->created_by   = $created_by;
    $support_category->save();

    return $support_category;
  }
  
  /**
   * @param array $ids
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $id) {
      $this->delete($id, $deleted_by);
    }
  }
  
  /**
   * @param int $id
   * @param int $deleted_by
   * @return bool
  */
  public function delete(int $id, int $deleted_by): bool
  {
    if(!$support_category = SupportCategory::find($id)) {
      throw new Exception('Support Category not found');
    }

    $support_category->delete();
    
    return true;
  }
    
  /**
   * @param int $support_category_id
   * @param int $status
   * @param int $updated_by
   * @return SupportCategory
  */
  public function updateStatus(int $support_category_id, int $status, int $updated_by): SupportCategory
  {
    if(!$support_category = SupportCategory::find($support_category_id)) {
      throw new Exception('Support Category not found');
    }

    $support_category->update(['status' => $status]);
    return $support_category;
  }
}