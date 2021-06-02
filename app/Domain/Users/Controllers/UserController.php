<?php

namespace App\Domain\Users\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Users\Services\UserService;
use App\Domain\Tattoos\Services\TattooService;
use App\Domain\Users\Requests\UpdateEmailRequest;
use App\Domain\Users\Requests\ChangePasswordRequest;
use App\Domain\Users\Requests\DeleteAccountResponseRequest;

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

  public function changePassword(ChangePasswordRequest $request)
  {
    if( $this->service->changeSelfPassword($request->old_password, $request->password, Auth::user()) ) {
      return response()->json([
        'status' => true,
        'message' => 'Password changed successfully',
      ], 201);
    }

    return response()->json([
      'status' => false,
      'message' => 'Failed to change the password',
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

  public function deleteRequest()
  {
    if( $this->service->deleteUserRequest(Auth::user()) ) {
      return response()->json([
        'status' => true,
        'message' => 'A request to delete the account completed successfully',
      ], 201);
    }

    return response()->json([
      'status' => false,
      'message' => 'Failed to create the delete account request',
    ], 400);
  }

  public function deleteResponse(DeleteAccountResponseRequest $request)
  {
    if( $this->service->deleteUserResponse($request->email, $request->token, $request->status) ) {
      return response()->json([
        'status' => true,
        'message' => 'The account was deleting response received successfully',
      ], 201);
    }

    return response()->json([
      'status' => false,
      'message' => 'Failed to delete the account',
    ], 400);
  }

  public function updateEmail(UpdateEmailRequest $request)
  {
    if( $this->service->updateEmailRequest(Auth::user(), $request->email, $request->password) ) {
      return response()->json([
        'status' => true,
        'message' => 'A request to update an email completed sucessfully',
      ], 201);
    }

    return response()->json([
      'status' => false,
      'message' => 'Failed to create the update email request',
    ], 400);
  }
}