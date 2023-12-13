<?php

namespace App\Domain\Users\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Users\Services\UserChallengeService;

class UserExternalService
{
    private LogService $log_service;

    public function __construct()
    {
        $this->log_service = new LogService('user');
    }
    
    /**
     * @param int $challenge_id
     * @return ?array
    */
    public function getChallengeAttemptsById(int $challenge_id): ?array
    {
        try {
            $user_challenge_service = new UserChallengeService();
            return $user_challenge_service->getChallengeAttemptsById($challenge_id)->toArray();
        } catch(Exception $ex) {
            $this->log_service->error($ex);
            return null;
        }
    }

    /**
     * @param array $challenges_id
     * @return ?array
    */
    public function getUserChallengesCounters(array $challenges_id): ?array
    {
        try {
            $user_challenge_service = new UserChallengeService();
            return $user_challenge_service->getUserChallengesCounters($challenges_id);
        } catch(Exception $ex) {
            $this->log_service->error($ex);
            return null;
        }
    }

    /**
     * @param int $challenge_id
     * @param int $user_id
     * @return ?array
    */
    public function getUserChallengeProgress(int $challenge_id, int $user_id): ?array
    {
        try {
            $user_challenge_service     = new UserChallengeService();
            $user_challenge_progress    = $user_challenge_service->getUserChallengeProgress($challenge_id, $user_id);
            return $user_challenge_progress ? $user_challenge_progress->toArray() : null;
        } catch(Exception $ex) {
            $this->log_service->error($ex);
            return null;
        }
    }
}