<?php

namespace App\Domain\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Users\Services\UserService;
use App\Domain\Users\Requests\LoginRequest;
use App\Domain\Users\Requests\SignupRequest;
use App\Domain\Users\Requests\ResetPasswordRequest;
use App\Domain\Users\Requests\ChangePasswordRequest;
use App\Domain\Users\Requests\ForgotPasswordRequest;

class AuthController extends Controller
{
  public function __construct()
  {
    $this->service = new UserService;
  }

  public function login(LoginRequest $request)
  {
    $res = $this->service->login($request->email, $request->password);
    if( is_array($res) ) {
      return response()->json([
        'status' => true,
        'message' => 'User logged in successfully',
        'data' => $res
      ], 200);
    }
    
    if( is_string($res) ) {
      return response()->json([
        'status' => false,
        'message' => $res,
      ], 422);
    }
    
    return response()->json([
      'status' => false,
      'message' => 'Failed to log in',
    ], 400);
  }

  public function signup(SignupRequest $request)
  {
    if( $res = $this->service->signup($request->validated()) ) {
      return response()->json([
        'status' => true,
        'message' => 'User created successfully',
        'data' => $res
      ], 201);
    }

    return response()->json([
      'status' => false,
      'message' => 'Failed to create the user',
    ], 400);
  }

  public function resetPassword(ResetPasswordRequest $request)
  {
    return response()->json(123);
  }

  public function forgotPassword(ForgotPasswordRequest $request)
  {
    if( $this->service->forgotPassword($request->email) ) {
      return response()->json([
        'status' => true,
        'message' => 'Forgot password finished successfully',
      ], 200);
    }

    return response()->json([
      'status' => false,
      'message' => 'Failed to finish the forgot password process',
    ], 400);
  }

  public function changePassword(ChangePasswordRequest $request)
  {
    
  }
}