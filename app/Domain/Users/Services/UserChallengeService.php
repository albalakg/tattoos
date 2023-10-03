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

    private LogService $log_service;

    public function __construct(
        ?ContentService $content_service = null,
    )
    {
        $this->content_service  = $content_service;
        $this->log_service      = new LogService('users');
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
                                        ->get();

        $challenges = $this->content_service->getChallenges($user_challenges->pluck('id')->toArray());

        foreach($user_challenges AS $user_challenge) {
            $user_challenge->challenge = $challenges->where('id', $user_challenge->challenge_id)->first();
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

        // $challenges = $this->content_service->getChallenges($user_challenges->pluck('id')->toArray());

        // foreach($user_challenges AS $user_challenge) {
        //     $user_challenge->challenge = $challenges->where('id', $user_challenge->challenge_id)->first();
        // }

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
        foreach($user_challenges AS $user_challenge) {
            if(empty($result[$user_challenge->challenge_id])) {
                $user_challenge[$user_challenge->challenge_id] = 1;
            } else {
                $user_challenge[$user_challenge->challenge_id]++;
            }
        }

        return $result;
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
     * @return void
     */
    public function submitChallenge(array $data, int $user_id)
    {
        if(!$this->content_service->isChallengeActive($data['challenge_id'])) {
            $this->log_service->error('Attempt to submit a challenge to non-active challenge', ['id' => $data['challenge_id']]);
            throw new Exception('Not allowed to submit the challenge');
        }

        if(!$user_challenge = $this->getUserChallenge($data['challenge_id'], $user_id)) {
            $user_challenge = $this->createUserChallenge($data['challenge_id'], $user_id);
        }

        $this->createUserChallengeAttempt($user_challenge->id, $data['video'], $data['is_public']);
    }
    
    /**
     * @param int $challenge_id
     * @param int $user_id
     * @return ?UserChallenge
    */
    private function getUserChallenge(int $challenge_id, int $user_id): ?UserChallenge
    {
        return UserChallenge::where('challenge_id', $challenge_id)
                            ->where('user_id', $user_id)
                            ->first();
    }
    
    /**
     * Create the record that related the user to the challenge without the attempt it self
     *
     * @param int $challenge_id
     * @param int $user_id
     * @return UserChallenge
    */
    private function createUserChallenge(int $challenge_id, int $user_id): UserChallenge
    {
        $user_challenge                 = new UserChallenge();
        $user_challenge->user_id        = $user_id;
        $user_challenge->challenge_id   = $challenge_id;
        $user_challenge->status         = StatusService::PENDING;
        $user_challenge->save();

        return $user_challenge;
    }
    
    /**
     * @param int $user_challenge_id
     * @param mixed $video
     * @param int $is_public
     * @return UserChallengeAttempt
    */
    private function createUserChallengeAttempt(int $user_challenge_id, $video, int $is_public): UserChallengeAttempt
    {
        $user_challenge_attempt                     = new UserChallengeAttempt();
        $user_challenge_attempt->user_challenge_id  = $user_challenge_id;
        $user_challenge_attempt->is_public          = $is_public;
        $user_challenge_attempt->status             = StatusService::PENDING;
        $user_challenge_attempt->video              = FileService::create($video, 'content/challenges/users', FileService::S3_DISK);
        $user_challenge_attempt->save();

        $this->log_service->info('New challenge attempt', $user_challenge_attempt->toArray());
        return $user_challenge_attempt;
    }
}