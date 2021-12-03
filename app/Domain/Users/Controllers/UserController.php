<?php

namespace App\Domain\Users\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Helpers\StatusService;
use App\Domain\Users\Services\UserService;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Content\Services\ContentService;
use App\Domain\Support\Services\SupportService;
use App\Domain\Users\Requests\CreateUserRequest;
use App\Domain\Users\Requests\UpdateUserRequest;
use App\Domain\Users\Requests\DeleteUsersRequest;
use App\Domain\Users\Requests\UpdateProfileRequest;
use App\Domain\Users\Requests\ChangePasswordRequest;
use App\Domain\Users\Requests\UpdateUserEmailRequest;
use App\Domain\Support\Services\SupportCategoryService;
use App\Domain\Users\Requests\UserLessonProgressRequest;
use App\Domain\Users\Requests\UpdateUserPasswordRequest;

class UserController extends Controller
{
  const LOG_FILE = 'users';
  
  public function logout(UserService $user_service)
  {
    try {
      $user_service->logout(Auth::user());
      return $this->successResponse('Logged out');
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function changePassword(ChangePasswordRequest $request, UserService $user_service)
  {
    try {
      $user_service->changePassword(Auth::user(), $request->old_password, $request->password);
      return $this->successResponse('User\'s password has updated');
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function updateUserEmail(UpdateUserEmailRequest $request, UserService $user_service)
  {
    try {
      $user_service->updateUserEmail($request->validated(), Auth::user()->id);
      return $this->successResponse('User\'s email updated');
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function updateUserPassword(UpdateUserPasswordRequest $request, UserService $user_service)
  {
    try {
      $user_service->updateUserPassword($request->validated(), Auth::user()->id);
      return $this->successResponse('User\'s password updated');
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateUserRequest $request, UserService $user_service)
  {
    try {
      $response = $user_service->createUserByAdmin($request->validated(), Auth::user()->id);
      return $this->successResponse('User created', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function updateUser(UpdateUserRequest $request, UserService $user_service)
  {
    try {
      $response = $user_service->updateUser($request->validated(), Auth::user()->id);
      return $this->successResponse('User updated', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function updateProfile(UpdateProfileRequest $request, UserService $user_service)
  {
    try {
      $updated_user = $user_service->updateProfile($request->validated(), Auth::user()->id);
      return $this->successResponse('User updated', $updated_user);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function delete(DeleteUsersRequest $request, UserService $user_service)
  {
    try {
      $response = $user_service->deleteUsers($request->ids, Auth::user()->id);
      return $this->successResponse('Users deleted', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function getAll(UserService $user_service)
  {
    try {
      $response = $user_service->getAll();
      return $this->successResponse('Users fetched', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function getProfile()
  {
    try {
      return $this->successResponse('Users profile fetched', Auth::user());
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function getUserActiveCourses()
  {
    try {
      $user_service = new UserService(
        new ContentService
      );
      
      $response = $user_service->getUserCourses(Auth::user(), StatusService::ACTIVE);
      return $this->successResponse('Fetched user active courses', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function getUserProgress()
  {
    try {
      $user_service = new UserService();
      $response = $user_service->getUserProgress(Auth::user(), StatusService::ACTIVE);
      return $this->successResponse('Fetched user progress', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
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
      return $this->successResponse('Fetched user orders', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
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
      return $this->successResponse('Fetched user support tickets', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function getUserFavoriteContent()
  {
    try {
      $user_service = new UserService(
        new ContentService
      );

      $response = $user_service->getUserFavoriteContent(Auth::user(), StatusService::ACTIVE);
      return $this->successResponse('Fetched user favorite content', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function setLessonProgress(UserLessonProgressRequest $request, UserService $user_service)
  {
    try {
      $response = $user_service->setLessonProgress($request->lesson_id, $request->progress, Auth::user()->id);
      return $this->successResponse('Set the user\'s lesson progress', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }
}