<?php

namespace App\Domain\Users\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Users\Services\UserService;
use App\Domain\Users\Requests\CreateUserRequest;
use App\Domain\Users\Requests\UpdateUserRequest;
use App\Domain\Users\Requests\DeleteUsersRequest;
use App\Domain\Users\Requests\ChangePasswordRequest;
use App\Domain\Users\Requests\UpdateUserEmailRequest;
use App\Domain\Users\Requests\UpdateUserPasswordRequest;

class UserController extends Controller
{
  public function __construct()
  {
    $this->service = new UserService;
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

  public function changePassword(ChangePasswordRequest $request, UserService $user_service)
  {
    try {
      $user_service->changePassword(Auth::user(), $request->old_password, $request->password);
      return $this->successResponse('Logged out successfully');
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function updateUserEmail(UpdateUserEmailRequest $request, UserService $user_service)
  {
    try {
      $user_service->updateUserEmail((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('User\'s email updated successfully');
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function updateUserPassword(UpdateUserPasswordRequest $request, UserService $user_service)
  {
    try {
      $user_service->updateUserPassword((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('User\'s password updated successfully');
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function create(CreateUserRequest $request, UserService $user_service)
  {
    try {
      $response = $user_service->createUser((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('User created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function update(UpdateUserRequest $request, UserService $user_service)
  {
    try {
      $response = $user_service->updateUser((object) $request->validated(), Auth::user()->id);
      return $this->successResponse('User updated successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function delete(DeleteUsersRequest $request, UserService $user_service)
  {
    try {
      $response = $user_service->deleteUsers($request->ids, Auth::user()->id);
      return $this->successResponse('Users deleted successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function getAll(UserService $user_service)
  {
    try {
      $response = $user_service->getAll();
      return $this->successResponse('Users fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }
}