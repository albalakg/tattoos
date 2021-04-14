<?php

namespace App\Domain\Users\Controllers;

use App\Domain\Tattoos\Services\TattooService;
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

  public function getTattoos()
  {
    if( $res = $this->service->getUserTattoos(Auth::user()->id, new TattooService) ) {
      return response()->json([
        'status' => true,
        'message' => 'Got user tattoos successfully',
        'data' => $res
      ], 201);
    }

    return response()->json([
      'status' => false,
      'message' => 'Failed to get the user tattoos',
    ], 400);
  }
}