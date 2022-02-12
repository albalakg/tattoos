<?php

namespace App\Domain\Users\Services;

use Exception;
use App\Domain\Users\Models\User;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Facades\Auth;
use App\Domain\Helpers\MaintenanceService;
use App\Domain\Users\Models\UserLogAttempt;

class LoginService
{        
    /**
     * @var object
    */
    private $response;

    /**
     * @var LogService
    */
    private $log_service;
    
    /**
     * @var bool
    */
    private $is_maintenance;
    
    /**
     * @var User
    */
    private $user;

    /**
     * Login to the application
     * 
     * @param string $email
     * @param string $password
     * @return void
    */
    public function __construct()
    {
        $this->log_service = new LogService('auth');
        $this->is_maintenance = MaintenanceService::isActive();
    }
    
    /**
     * attempt
     *
     * @param string $email
     * @param string $password
     * @return self
    */
    public function attempt(string $email, string $password): self
    {
        $this->log_service->info('Attempt login with email: ' . $email);
        $attempt = Auth::attempt(['email' => $email, 'password' => $password]);
        if(!$attempt) {
            $this->errorLog('Email or password is invalid');
        }

        $this->user = Auth::user();
        
        $this->isUserAuthorizedToAccess();
        $this->buildUserDetails();

        return $this;
    }
    
    /**
     * @return object
    */
    public function getResponse(): object
    {
        return $this->response;
    }
    
    /**
     * check if the user is authorized to login
     *
     * @return void
    */
    private function isUserAuthorizedToAccess()
    {
        if($this->user->isInactive()) {
            $this->errorLog('User is unauthorized to login');
        }

        if($this->user->isWaitingForConfirmation()) {
            $this->errorLog('Please confirm your email first');
        }

        if($this->is_maintenance && !$this->user->isAdmin()) {
            $this->errorLog('Sorry, Unauthorized to login during maintenance mode');
        }
    }
    
    /**
     * Build the user details object
     *
     * @return void
    */
    private function buildUserDetails()
    {
        $this->response = (object)[
            'id'            => $this->user->id,
            'first_name'    => $this->user->details->first_name,
            'last_name'     => $this->user->details->last_name,
            'email'         => $this->user->email,
            'phone'         => $this->user->details->phone,
            'gender'        => $this->user->details->gender,
            'birth_date'    => $this->user->details->birth_date,
            'role'          => $this->user->role->name,
            'expired_at'    => now()->addMinutes(config('session.lifetime')),
            'token'         => $this->setUserToken()
        ];
    }
    
    /**
     * Create and set the token
     *
     * @return string
    */
    private function setUserToken(): string
    {
        return $this->user->createToken(config('app.name'))->accessToken;
    }
    
    
    /**
     * @param string $message
     * @return void
    */
    private function errorLog(string $message)
    {
        $this->log_service->error($message);
        throw new Exception($message);
    }
}