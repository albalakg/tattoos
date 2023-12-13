<?php

namespace App\Domain\Users\Services;

use Exception;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Facades\DB;
use App\Domain\Helpers\FileService;
use App\Domain\Helpers\StatusService;
use App\Domain\Users\Models\UserChallenge;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Content\Services\ContentService;
use App\Domain\Users\Models\UserChallengeAttempt;

class UserChallengeService
{
    private ContentService|null $content_service;

    private SubmitChallengeService|null $submit_challenge_service;

    private LogService $log_service;

    public function __construct(
        ?ContentService $content_service = null,
        ?SubmitChallengeService $submit_challenge_service = null
    ) {
        $this->content_service          = $content_service;
        $this->submit_challenge_service = $submit_challenge_service;
        $this->log_service              = new LogService('users');
    }

    /**
     * @param int $user_id
     * @return Collection
     */
    public function getUserChallenges(int $user_id): Collection
    {
        $user_challenges = UserChallenge::where('user_id', $user_id)
            ->with('lastAttempt')
            ->select('id', 'challenge_id', 'status')
            ->withCount('attempts')
            ->get();

        if($challenges = $this->content_service->getChallenges($user_challenges->pluck('challenge_id')->toArray())) {
            foreach ($user_challenges as $user_challenge) {
                if($user_challenge->lastAttempt) {
                    $user_challenge->lastAttempt->human_time = $user_challenge->lastAttempt->created_at->diffForHumans();
                }
                $user_challenge->challenge  = $challenges->where('id', $user_challenge->challenge_id)->first();
            }
        }
        
        return $user_challenges;
    }

    /**
     * @param int $user_id
     * @return Collection
     */
    public function getUserChallengesHistory(int $user_id): Collection
    {
        $user_challenges = UserChallenge::where('user_id', $user_id)
            ->with('attempts')
            ->select('id', 'challenge_id', 'status', 'created_at')
            ->get();

        return $user_challenges;
    }

    /**
     * @param array $challenges_id
     * @return array
     */
    public function getUserChallengesCounters(array $challenges_id): array
    {
        $user_challenges = UserChallenge::whereIn('challenge_id', $challenges_id)
            ->select('id', 'challenge_id')
            ->get();

        $result = [];

        foreach ($user_challenges as $user_challenge) {
            if (empty($result[$user_challenge->challenge_id])) {
                $result[$user_challenge->challenge_id] = 1;
            } else {
                $result[$user_challenge->challenge_id]++;
            }
        }

        return $result;
    }

    /**
     * @param int $challenge_id
     * @param int $user_id
     * @return ?UserChallenge
     */
    public function getUserChallengeProgress(int $challenge_id, int $user_id): ?UserChallenge
    {
        return UserChallenge::where('challenge_id', $challenge_id)
            ->with('attempts')
            ->select('id', 'challenge_id', 'status', 'created_at')
            ->first();
    }

    /**
     * @param int $challenge_id
     * @return Collection
     */
    public function getChallengeAttemptsById(int $challenge_id): Collection
    {
        return UserChallenge::where('challenge_id', $challenge_id)
            ->join('user_details', 'user_details.user_id', 'user_challenges.user_id')
            ->select('user_challenges.id', 'challenge_id', 'user_challenges.user_id', 'status', 'created_at', DB::raw('CONCAT(first_name, " ", last_name) AS full_name'))
            ->with('attempts')
            ->get();
    }

    /**
     * @param int $user_challenge_id
     * @param int $status
     * @return void
     */
    public function updateUserChallengeStatus(int $user_challenge_id, int $status)
    {
        UserChallenge::where('id', $user_challenge_id)
            ->update([
                'status' => $status
            ]);

        UserChallengeAttempt::where('user_challenge_id', $user_challenge_id)
            ->update([
                'status' => $status
            ]);

        $this->log_service->info('User challenge status has been updated', ['status' => $status]);
    }

    /**
     * @param array $data
     * @param int $user_id
     * 
     * @throws Exception
     * @return UserChallenge
     */
    public function submitChallenge(array $data, int $user_id): UserChallenge
    {
        try {
            $user_challenge = $this->submit_challenge_service
                                    ->attempt($user_id, $data['id'])
                                    ->validateIfCanSubmit()
                                    ->submit($data)
                                    ->getUserChallenge();

            $user_challenge->loadCount('attempts');
            $user_challenge->lastAttempt->human_time = $user_challenge->lastAttempt->created_at->diffForHumans();

            if($challenges = $this->content_service->getChallenges([$user_challenge->challenge_id])) {
                $user_challenge->challenge = $challenges->where('id', $user_challenge->challenge_id)->first();
            }
            
            return $user_challenge;
        } catch(Exception $ex) {
            $this->log_service->error($ex->getMessage(), ['id' => $data['id']]);
            throw new Exception($ex->getMessage());
        }
    }
}
