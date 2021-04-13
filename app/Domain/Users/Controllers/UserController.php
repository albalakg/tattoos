<?php

namespace App\Domain\Users\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Users\Services\UserService;

class UserController extends Controller
{
  public function __construct()
  {
    $this->service = new UserService;
  }

  public function logout()
  {
    $this->service->logout();
    return response()->json([
      'status' => true,
      'message' => 'User logged out successfully',
    ], 400);
  }

  public function details()
  {
    return response()->json(Auth::user());
  }
}