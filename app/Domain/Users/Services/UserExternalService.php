<?php

namespace App\Domain\Users\Services;

use App\Domain\Users\Services\UserChallengeService;

class UserExternalService
{
    /**
     * @param int $challenge_id
     * @return array
    */
    public function getChallengeAttemptsById(int $challenge_id): array
    {
        $user_challenge_service = new UserChallengeService();
        return $user_challenge_service->getChallengeAttemptsById($challenge_id)->toArray();
    }

    /**
     * @param array $challenges_id
     * @return array
    */
    public function getUserChallengesCounters(array $challenges_id): array
    {
        $user_challenge_service = new UserChallengeService();
        return $user_challenge_service->getUserChallengesCounters($challenges_id);
    }
}