<?php

namespace App\Domain\Users\Services;

use Exception;
use App\Domain\Users\Models\User;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Facades\Auth;
use App\Domain\Helpers\MaintenanceService;
use App\Domain\Users\Models\UserLogAttempt;
use App\Domain\Users\Requests\LoginRequest;

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
     * @param LoginRequest $request
     * @return self
    */
    public function attempt(LoginRequest $request): self
    {
        $attempt = Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')]);
        $this->writeLoginAttempt($request, $attempt);

        if(!$attempt) {
            throw new Exception('Email or password is invalid');
        }

        $this->user = Auth::user();
        $this->log_service->info('User ' . $this->user->id . ' logged in');
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
            $this->log_service->info('User ' . $this->user->id . ' is unauthorized to login');
            throw new Exception('Unauthorized to login');
        }

        if($this->user->isWaitingForConfirmation()) {
            $this->log_service->info('User ' . $this->user->id . ' must confirm the email');
            throw new Exception('Please confirm your email first');
        }

        if($this->is_maintenance && !$this->user->isAdmin()) {
            $this->log_service->info('User ' . $this->user->id . ' can\'t access while in maintenance');
            throw new Exception('Sorry, Unauthorized to login during maintenance mode');
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
     * @param LoginRequest $request
     * @param bool $attempt
     * @return void
    */
    private function writeLoginAttempt(LoginRequest $request, bool $attempt)
    {
        UserLogAttempt::create([
            'email'         => $request->input('email'),
            'status'        => $attempt,
            'user_agent'    => $request->header('user-agent'),
            'ip'            => $request->ip(),
            'created_at'    => now()
        ]);
    }
}