<?php

namespace App\Domain\Content\Services;

use Exception;
use Illuminate\Support\Str;
use App\Domain\Helpers\LogService;
use App\Domain\Content\Models\Coupon;
use App\Domain\Helpers\StatusService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CouponService
{

  /**
   * @var LogService
  */
  private $log_service;

  public function __construct()
  {
    $this->log_service = new LogService('coupons');
  }

  /**
   * @param int $coupon_id
   * @return Coupon|null
  */
  public function getById(int $coupon_id): ?Coupon
  {
    return Coupon::find($coupon_id);
  }

  /**
   * @param string $code
   * @return Coupon|null
  */
  public function getByCode(string $code): ?Coupon
  {
    $coupon = Coupon::where('code', $code)
                 ->where('status', StatusService::ACTIVE)
                 ->select('id', 'type', 'value')
                 ->first();
                 
    if(!$coupon) {
      return null;
    }

    $coupon->type = '%';
    return $coupon;
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
   * @param array $data
   * @param int $created_by
   * @return Coupon|null 
  */
  public function create(array $data, int $created_by): ?Coupon
  {
    $coupon             = new Coupon;
    $coupon->code       = $this->generateCode();
    $coupon->type       = $data['type'];
    $coupon->value      = $data['value'];
    $coupon->status     = StatusService::PENDING;
    $coupon->created_by = $created_by;
    $coupon->save();


    $this->log_service->info('Coupon has been created: ' . json_encode($coupon));

    return $coupon;
  }
    
  /**
   * @param array $data
   * @param int $updated_by
   * @return Coupon|null 
  */
  public function update(array $data, int $updated_by): ?Coupon
  {
    $coupon             = Coupon::find($data['id']);
    $coupon->type       = $data['type'];
    $coupon->value      = $data['value'];
    $coupon->status     = $data['status'];
    $coupon->save();

    $this->log_service->info('Coupon has been updated: ' . json_encode($coupon));

    return $coupon;
  }
  
  /**
   * @param int $id
   * @param int $status
   * @param int $updated_by
   * @return bool
  */
  public function updateStatus(int $id, int $status, int $updated_by): bool
  {
    $result = Coupon::where('id', $id)->update([
      'status' => $status
    ]);

    $this->log_service->info('Coupon ' . $id . ' status has been updated to ' . $status);

    return $result;
  }
  
  /**
   * @param string $path
   * @param int $deleted_by
   * @return void
  */
  public function multipleDelete(array $ids, int $deleted_by)
  {
    foreach($ids AS $coupon_id) {
      $this->delete($coupon_id, $deleted_by);
    }
  } 
  
  /**
   * Soft delete the item 
   * @param int $coupon_id
   * @param int $deleted_by
   * @return bool
  */
  public function delete(int $coupon_id, int $deleted_by): bool
  {
    $result = Coupon::where('id', $coupon_id)->delete();
    $this->log_service->info('Coupon ' . $coupon_id . ' has been deleted');
    return $result;
  }
  
  /**
   * @param int $coupon_id
   * @param int $deleted_by
   * @return bool
  */
  public function forceDelete(int $coupon_id, int $deleted_by): bool
  {
    $result = Coupon::where('id', $coupon_id)->forceDelete();
    $this->log_service->info('Coupon ' . $coupon_id . ' has been force deleted');
    return $result;
  }
  
  /**
   * Build base query
   *
   * @return Builder
  */   
  private function baseQueryBuilder(): Builder
  {
    return Coupon::query();
  }
  
  /**
   * @return string
  */   
  private function generateCode(): string
  {
    for($attempt = 0; $attempt < 5; $attempt++) {
      $code = strtoupper(Str::random(Coupon::CODE_LENGTH));
      if(!Coupon::where('code', $code)->exists()) {
        return $code;
      }
    }

    throw new Exception('Failed to generate a code');
  }
}