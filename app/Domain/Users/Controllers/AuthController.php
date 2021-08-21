<?php

namespace App\Domain\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Users\Requests\LoginRequest;
use App\Domain\Users\Services\LoginService;
use App\Domain\Users\Requests\SignupRequest;
use App\Domain\Users\Requests\VerifyEmailRequest;
use App\Domain\Users\Requests\ResetPasswordRequest;
use App\Domain\Users\Requests\ForgotPasswordRequest;
use App\Domain\Users\Services\UserService;
use Exception;

class AuthController extends Controller
{
  public function login(LoginRequest $request, LoginService $login_service)
  {
    try {
      $login_service->attempt($request->email, $request->password);
      $this->successResponse('Logged successfully', $login_service->getResponse());
    } catch (Exception $ex) {
      $this->errorResponse($ex->getMessage());
    }
  }

  public function signup(SignupRequest $request, UserService $user_service)
  {
    $user_service->signup($request->validated());
  }

  public function logout()
  {
  
  }

  public function resetPassword(ResetPasswordRequest $request)
  {
   
  }

  public function forgotPassword(ForgotPasswordRequest $request)
  {
  
  }

  public function verifyEmail(VerifyEmailRequest $request)
  {
 
  }
}