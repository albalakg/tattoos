<?php

namespace App\Domain\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Users\Requests\LoginRequest;
use App\Domain\Users\Requests\SignupRequest;
use App\Domain\Users\Requests\ChangePasswordRequest;
use App\Domain\Users\Requests\ForgotPasswordRequest;

class AuthController extends Controller
{
  public function login(LoginRequest $request)
  {
    
  }

  public function signup(SignupRequest $request)
  {

  }

  public function forgotPassword(ForgotPasswordRequest $request)
  {

  }

  public function changePassword(ChangePasswordRequest $request)
  {

  }
}