<?php

namespace App\Domain\Users\Controllers;

use Exception;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Users\Services\UserService;
use App\Domain\Users\Requests\LoginRequest;
use App\Domain\Users\Services\LoginService;
use App\Domain\Users\Requests\SignupRequest;
use App\Domain\Users\Requests\VerifyEmailRequest;
use App\Domain\Users\Requests\ResetPasswordRequest;
use App\Domain\Users\Requests\ForgotPasswordRequest;

class AuthController extends Controller
{
  public function login(LoginRequest $request, LoginService $login_service)
  {
    try {
      return $this->successResponse('Logged successfully', $login_service->attempt($request->email, $request->password)->getResponse());
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function signup(SignupRequest $request, UserService $user_service)
  {
    try {
      $user_service->signup((object) $request->validated());
      return $this->successResponse('Registered successfully');
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function logout(UserService $user_service)
  {
    try {
      $user_service->logout(Auth::user());
      return $this->successResponse('Logged out successfully');
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function resetPassword(ResetPasswordRequest $request, UserService $user_service)
  {
    try {
      $user_service->resetPassword($request->email, $request->token, $request->password);
      return $this->successResponse('Registered successfully');
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function forgotPassword(ForgotPasswordRequest $request, UserService $user_service)
  {
    try {
      $user_service->forgotPassword($request->email);
      return $this->successResponse('Registered successfully');
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function verifyEmail(VerifyEmailRequest $request, UserService $user_service)
  {
    try {
      $user_service->verifyEmail($request->email, $request->token);
      return $this->successResponse('Email verified successfully');
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage(), null, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
  }
}