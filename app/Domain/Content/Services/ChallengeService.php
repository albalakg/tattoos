<?php

namespace App\Domain\Content\Services;

use Exception;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Collection;
use App\Domain\Content\Models\Challenge;

class ChallengeService
{
  /**
   * @var LogService
  */
  private $log_service;

  public function __construct()
  {
    $this->log_service = new LogService('challenges');
  }

  /**
   * @return Collection
  */
  public function getAll(): Collection
  {
    return Challenge::query()
                    ->orderBy('id', 'desc')
                    ->get();
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
}