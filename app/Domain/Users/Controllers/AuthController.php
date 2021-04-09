<?php

namespace App\Domain\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Users\Requests\LoginRequest;
use App\Domain\Users\Requests\SignupRequest;
use App\Domain\Users\Requests\ChangePasswordRequest;
use App\Domain\Users\Requests\ForgotPasswordRequest;
use App\Domain\Users\Services\UserService;

class AuthController extends Controller
{
  public function __construct()
  {
    $this->service = new UserService;
  }

  public function login(LoginRequest $request)
  {
    
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

  public function forgotPassword(ForgotPasswordRequest $request)
  {

  }

  public function changePassword(ChangePasswordRequest $request)
  {

  }
}