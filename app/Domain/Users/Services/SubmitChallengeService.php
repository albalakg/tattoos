<?php

namespace App\Domain\Users\Services;

use Exception;
use App\Domain\Helpers\FileService;
use App\Domain\Helpers\StatusService;
use App\Domain\Users\Models\UserChallenge;
use App\Domain\Content\Services\ContentService;
use App\Domain\Users\Models\UserChallengeAttempt;

class SubmitChallengeService
{
    const MAXIMUM_ATTEMPTS = 3;

    private int $user_id;
    
    private int $challenge_id;
    
    private ContentService $content_service;
    
    private ?UserChallenge $user_challenge;
    
    /**
     * @param ContentService $contentService
     * @return void
    */
    public function __construct(ContentService $content_service)
    {
        $this->content_service = $content_service;
    }
    
    /**
     * @param int $user_id
     * @param int $challenge_id
     * @return self
    */
    public function attempt(int $user_id, int $challenge_id): self
    {
        $this->user_id      = $user_id;
        $this->challenge_id = $challenge_id;
        
        $this->validateIfCanSubmit();

        return $this;
    }
    
    /**
     * @throws Exception
     * @return self
    */
    public function validateIfCanSubmit(): self
    {
        if (!$this->content_service->isChallengeActive($this->challenge_id)) {
            throw new Exception('Challenge is not active');
        }

        if($this->isReachedMaximumAttempts()) {
            throw new Exception('Not allowed to submit the challenge');
        }

        if($this->hasAttemptInPendingOrCompleted()) {
            throw new Exception('Has attempt that already in pending or completed status');
        }

        return $this;
    }
        
    /**
     * @param array $data
     * @return self
    */
    public function submit(array $data): self
    {
        $this->createUserChallenge();
        $this->user_challenge->lastAttempt = $this->createUserChallengeAttempt($data);
        return $this;
    }
        
    /**
     * @return UserChallenge
    */
    public function getUserChallenge(): UserChallenge
    {
        return $this->user_challenge;
    }
    
    /**
     * @return bool
     */
    private function isReachedMaximumAttempts(): bool
    {
        $total_attempts = UserChallenge::where('challenge_id', $this->challenge_id)
            ->where('user_id', $this->user_id)
            ->join('user_challenge_attempts', 'user_challenge_attempts.user_challenge_id', 'user_challenges.id')
            ->count();

        return $total_attempts >= self::MAXIMUM_ATTEMPTS;
    }
    
    /**
     * @return bool
     */
    private function hasAttemptInPendingOrCompleted(): bool
    {
        return UserChallenge::where('challenge_id', $this->challenge_id)
            ->where('user_id', $this->user_id)
            ->join('user_challenge_attempts', 'user_challenge_attempts.user_challenge_id', 'user_challenges.id')
            ->whereIn('user_challenge_attempts.status', [StatusService::PENDING, StatusService::ACTIVE])
            ->exists();
    }

    private function createUserChallenge()
    {
        $this->user_challenge = UserChallenge::firstOrCreate(
            [
                'user_id'       => $this->user_id,
                'challenge_id'  => $this->challenge_id,
            ],
            [
                'status' => StatusService::PENDING
            ]
        );
    }

    /**
     * @param array $data
     * @return UserChallengeAttempt
     */
    private function createUserChallengeAttempt(array $data): UserChallengeAttempt
    {
        $user_challenge_attempt                     = new UserChallengeAttempt();
        $user_challenge_attempt->user_challenge_id  = $this->user_challenge->id;
        $user_challenge_attempt->is_public          = $data['is_public'];
        $user_challenge_attempt->status             = StatusService::PENDING;
        $user_challenge_attempt->video              = FileService::create($data['video'], 'content/challenges/users', FileService::S3_DISK);
        $user_challenge_attempt->save();

        return $user_challenge_attempt;
    }
}