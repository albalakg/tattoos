<?php

namespace App\Domain\Users\Controllers;

use App\Domain\Helpers\LogService;
use Exception;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Users\Services\UserService;
use App\Domain\Users\Requests\LoginRequest;
use App\Domain\Users\Services\LoginService;
use App\Domain\Users\Requests\SignupRequest;
use App\Domain\Users\Services\UserCourseService;
use App\Domain\Users\Requests\VerifyEmailRequest;
use App\Domain\Users\Requests\ResetPasswordRequest;
use App\Domain\Users\Requests\ForgotPasswordRequest;

class AuthController extends Controller
{
  private LogService $log_service;

  public function __construct()
  {
    $this->log_service = new LogService('auth');
  }
  
  public function login(LoginRequest $request, LoginService $login_service)
  {
    try {
      $userData = $login_service->attempt($request)->getResponse();
      return $this->successResponse('Logged', $userData);
    } catch (Exception $ex) {
      if($ex->getCode() !== 422) {
        $ex->log_level = 'critical';
      }
      $ex->service = 'auth';
      return $this->errorResponse($ex, null, $ex->getCode());
    }
  }

  public function signup(SignupRequest $request)
  {
    try {
      $user_service = new UserService;
      $created_user = $user_service->signup($request->validated());
      return $this->successResponse('You have Signed Up Successfully', $created_user);
    } catch (Exception $ex) {
      $ex->service = 'auth';
      return $this->errorResponse($ex);
    }
  }

  public function resetPassword(ResetPasswordRequest $request)
  {
    try {
      $user_service = new UserService;
      $user_service->resetPassword($request->email, $request->token, $request->password);
      return $this->successResponse('You have reset your password');
    } catch (Exception $ex) {
      $ex->service = 'auth';
      return $this->errorResponse($ex);
    }
  }

  public function forgotPassword(ForgotPasswordRequest $request)
  {
    try {
      $user_service = new UserService;
      $user_service->forgotPassword($request->email);
      return $this->successResponse('An email has been sent to the requested address');
    } catch (Exception $ex) {
      $ex->service = 'auth';
      return $this->errorResponse($ex);
    }
  }

  public function verifyEmail(VerifyEmailRequest $request)
  {
    try {
      $user_service = new UserService;
      $user_service->verifyEmail($request->email, $request->token);
      return $this->successResponse('You have verified your email successfully');
    } catch (Exception $ex) {
      $ex->service = 'auth';
      return $this->errorResponse($ex, null, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
  }
}