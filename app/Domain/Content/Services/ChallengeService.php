<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Collection;
use App\Domain\Content\Models\Challenge;
use App\Domain\Users\Services\UserService;

class ChallengeService
{
  private LogService $log_service;

  private UserService $user_service;

  public function __construct(UserService $user_service)
  {
    $this->user_service = $user_service;
    $this->log_service  = new LogService('challenges');
  }

  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    $challenges = Challenge::query()
                    ->orderBy('id', 'desc')
                    ->get();

    $user_challenges = $this->user_service->getUserChallengesCounters($challenges->pluck('id')->toArray());

    return $challenges;
  }

  /**
   * Gets the last not expired challenge
   * @return Challenge
  */
  public function getActiveChallenge(): Challenge
  {
    return Challenge::query()
                    ->where('expired_at', '<', now())
                    ->orderBy('id', 'desc')
                    ->first();
  }

  /**
   * @param array $data
   * @return Challenge
  */
  public function update(array $data): Challenge
  {
    $challenge              = Challenge::find($data['id']);
    if(!$challenge) {
      throw new Exception();
    }

    $challenge->type        = $data['video_id'];
    $challenge->name        = $data['name'];
    $challenge->description = $data['description'];
    $challenge->expired_at  = $data['expired_at'];
    $challenge->save();

    $this->log_service->info('Challenge has been updated', $challenge->toArray());

    return $challenge;
  }

  /**
   * @param array $data
   * @return Challenge
  */
  public function create(array $data): Challenge
  {
    $challenge              = new Challenge();
    $challenge->type        = $data['video_id'];
    $challenge->name        = $data['name'];
    $challenge->description = $data['description'];
    $challenge->expired_at  = $data['expired_at'];
    $challenge->save();

    $this->log_service->info('Challenge has been updated', $challenge->toArray());

    return $challenge;
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
  public function delete(int $coupon_id): bool
  {
    $result = Challenge::where('id', $coupon_id)->delete();
    $this->log_service->info('Challenge has been deleted', ['id' => $coupon_id, 'result' => $result]);
    return $result;
  }
  
  /**
   * @param int $coupon_id
   * @param int $deleted_by
   * @return bool
  */
  public function forceDelete(int $coupon_id, int $deleted_by): bool
  {
    $result = Challenge::where('id', $coupon_id)->forceDelete();
    $this->log_service->info('Challenge has been force deleted', ['id' => $coupon_id, 'result' => $result]);
    return $result;
  }
}