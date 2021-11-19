<?php

namespace App\Domain\Users\Controllers;

use App\Domain\Content\Services\CourseService;
use App\Domain\Helpers\StatusService;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Support\Services\SupportCategoryService;
use App\Domain\Support\Services\SupportService;
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
  public function logout(UserService $user_service)
  {
    try {
      $user_service->logout(Auth::user());
      return $this->successResponse('Logged out successfully');
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function changePassword(ChangePasswordRequest $request, UserService $user_service)
  {
    try {
      $user_service->changePassword(Auth::user(), $request->old_password, $request->password);
      return $this->successResponse('Logged out successfully');
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function updateUserEmail(UpdateUserEmailRequest $request, UserService $user_service)
  {
    try {
      $user_service->updateUserEmail($request->validated(), Auth::user()->id);
      return $this->successResponse('User\'s email updated successfully');
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function updateUserPassword(UpdateUserPasswordRequest $request, UserService $user_service)
  {
    try {
      $user_service->updateUserPassword($request->validated(), Auth::user()->id);
      return $this->successResponse('User\'s password updated successfully');
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateUserRequest $request, UserService $user_service)
  {
    try {
      $response = $user_service->createUser($request->validated(), Auth::user()->id);
      return $this->successResponse('User created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function update(UpdateUserRequest $request, UserService $user_service)
  {
    try {
      $response = $user_service->updateUser($request->validated(), Auth::user()->id);
      return $this->successResponse('User updated successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function delete(DeleteUsersRequest $request, UserService $user_service)
  {
    try {
      $response = $user_service->deleteUsers($request->ids, Auth::user()->id);
      return $this->successResponse('Users deleted successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function getAll(UserService $user_service)
  {
    try {
      $response = $user_service->getAll();
      return $this->successResponse('Users fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function getUserActiveCourses()
  {
    try {
      $user_service = new UserService(
        new CourseService
      );
      
      $response = $user_service->getUserCourses(Auth::user(), StatusService::ACTIVE);
      return $this->successResponse('Fetched user active courses successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function getUserProgress()
  {
    try {
      $user_service = new UserService(
        new CourseService
      );

      $response = $user_service->getUserProgress(Auth::user(), StatusService::ACTIVE);
      return $this->successResponse('Fetched user progress successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function getUserOrders()
  {
    try {
      $user_service = new UserService(
        null,
        null,
        new OrderService
      );

      $response = $user_service->getUserOrders(Auth::user());
      return $this->successResponse('Fetched user orders successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function getUserSupportTickets()
  {
    try {
      $user_service = new UserService(
        null,
        new SupportService(
          new SupportCategoryService
        )
      );

      $response = $user_service->getUserSupportTickets(Auth::user(), StatusService::ACTIVE);
      return $this->successResponse('Fetched user support tickets successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function getUserFavoriteContent()
  {
    try {
      $user_service = new UserService(
        null,
        new SupportService(
          new SupportCategoryService
        )
      );

      $response = $user_service->getUserFavoriteContent(Auth::user(), StatusService::ACTIVE);
      return $this->successResponse('Fetched user support tickets successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}