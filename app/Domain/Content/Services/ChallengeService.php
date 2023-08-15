<?php

namespace App\Domain\Content\Services;

use Exception;
use Carbon\Carbon;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Collection;
use App\Domain\Helpers\StatusService;
use App\Domain\Content\Models\Challenge;
use App\Domain\Users\Services\UserService;
use App\Domain\Content\Models\ChallengeTrainingOption;

class ChallengeService
{
  private LogService $log_service;

  private ?UserService $user_service;

  private ?TrainingOptionService $training_option_service;

  public function __construct(
    ?UserService $user_service = null,
    ?TrainingOptionService $training_option_service = null
    )
  {
    $this->user_service             = $user_service;
    $this->training_option_service  = $training_option_service;
    $this->log_service              = new LogService('challenges');
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

    foreach($challenges AS $challenge) {
      $challenge['user_attempts'] = $user_challenges[$challenge->id] ?? 0;
    }

    return $challenges;
  }

  /**
   * Gets the last not expired challenge
   * @return Challenge
  */
  public function getActiveChallenge(): Challenge
  {
    return Challenge::query()
                    ->where('status', StatusService::ACTIVE)
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

    $challenge->video_id    = $data['video_id'];
    $challenge->name        = $data['name'];
    $challenge->status      = $data['status'];
    $challenge->description = $data['description'];
    $challenge->expired_at  = $data['expired_at'];
    $challenge->save();

    $this->log_service->info('Challenge has been updated', $challenge->toArray());

    return $challenge;
  }

  /**
   * @param array $data
   * @param int $created_by
   * @return Challenge
  */
  public function create(array $data, int $created_by): Challenge
  {
    $challenge              = new Challenge();
    $challenge->video_id    = $data['video_id'];
    $challenge->status      = $data['status'] ?? StatusService::PENDING;
    $challenge->name        = $data['name'];
    $challenge->description = $data['description'];
    $challenge->expired_at  = new Carbon($data['expired_at']);
    $challenge->created_by  = $created_by;
    $challenge->save();

    try {
      if(isset($data['options'])) {
        $this->assignOptions($challenge->id, $data['options'], $created_by);
      }
  
      $this->log_service->info('Challenge has been created', $challenge->toArray());
    } catch(Exception $ex) {
      $challenge->forceDelete();
      $this->log_service->error($ex, ['id' => $challenge->id]);
      throw new Exception('Failed to create a challenge: ' . $ex->getMessage());
    }

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
   
  /**
   * @param int $challenge_id
   * @param array $options
   * @param int $created_by
   * @return void
  */
  private function assignOptions(int $challenge_id, array $options, int $created_by)
  {
    if($this->training_option_service === null) {
      throw new Exception('Missing the Training Options Service');
    }

    $this->deleteChallengeOptions($challenge_id);

    if(!count($options)) {
      return;
    }

    if(!$this->training_option_service->trainingOptionsExist(collect($options)->pluck('id')->toArray())) {
      throw new Exception('One or more Training Options were not found, Training Options: ' . json_encode($options));
    }

    $this->addChallengeTrainingOptions($challenge_id, $options, $created_by);
  }
  
  /**
   * @param int $challenge_id
   * @return void
  */
  private function deleteChallengeOptions(int $challenge_id)
  {
    ChallengeTrainingOption::where('challenge_id', $challenge_id)->delete();
  }
  
  /**
   * @param int $challenge_id
   * @param array $options
   * @param int $created_by
   * @return void
  */
  private function addChallengeTrainingOptions(int $challenge_id, array $options, int $created_by)
  {
    $challenge_options = [];
    foreach($options AS $option) {
      $challenge_options[] = [
        'challenge_id'        => $challenge_id,
        'training_option_id'  => $option['id'],
        'value'               => $option['value'],
        'created_at'          => now(),
        'created_by'          => $created_by,
      ];
    }

    ChallengeTrainingOption::insert($challenge_options);
  }
}