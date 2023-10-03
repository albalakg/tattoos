<?php

namespace App\Domain\Users\Controllers;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\MailService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Helpers\StatusService;
use App\Domain\Users\Services\UserService;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Content\Services\ContentService;
use App\Domain\Support\Services\SupportService;
use App\Domain\Users\Requests\CreateUserRequest;
use App\Domain\Users\Requests\UpdateUserRequest;
use App\Domain\Content\Services\ChallengeService;
use App\Domain\Users\Requests\DeleteUsersRequest;
use App\Domain\Users\Requests\UpdateEmailRequest;
use App\Domain\Users\Requests\UserFavoriteRequest;
use App\Domain\Users\Services\UserFavoriteService;
use App\Domain\Users\Requests\UpdateProfileRequest;
use App\Domain\Users\Services\UserChallengeService;
use App\Domain\Users\Requests\ChangePasswordRequest;
use App\Domain\Users\Requests\UpdateUserEmailRequest;
use App\Domain\Support\Services\SupportCategoryService;
use App\Domain\Users\Requests\GetUserChallengesRequest;
use App\Domain\Users\Requests\UpdateUserPasswordRequest;
use App\Domain\Users\Requests\UserLessonProgressRequest;
use App\Domain\Users\Services\UserCourseScheduleService;
use App\Domain\Users\Requests\AddTrainingScheduleRequest;
use App\Domain\Users\Requests\GetActionLogsHistoryRequest;
use App\Domain\Users\Requests\LandedOnPageNotFoundRequest;
use App\Domain\Users\Requests\DeleteTrainingScheduleRequest;
use App\Domain\Users\Requests\UpdateTrainingScheduleRequest;
use App\Domain\Users\Requests\ScheduleUserCourseLessonRequest;
use App\Domain\Content\Requests\Challenge\SubmitChallengeRequest;

class UserController extends Controller
{
  const LOG_FILE = 'users';
  
  public function logout()
  {
    try {
      $user_service = new UserService;
      $user_service->logout(Auth::user());
      return $this->successResponse('Logged out');
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function changePassword(ChangePasswordRequest $request)
  {
    try {
      $user_service = new UserService;
      $user_service->changePassword(Auth::user(), $request->old_password, $request->password);
      return $this->successResponse('User\'s password has updated');
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function updateUserEmail(UpdateUserEmailRequest $request)
  {
    try {
      $user_service = new UserService;
      $user_service->updateUserEmail($request->validated(), Auth::user()->id);
      return $this->successResponse('User\'s email updated');
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function updateUserPassword(UpdateUserPasswordRequest $request)
  {
    try {
      $user_service = new UserService;
      $user_service->updateUserPassword($request->validated(), Auth::user()->id);
      return $this->successResponse('User\'s password updated');
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateUserRequest $request)
  {
    try {
      $user_service = new UserService;
      $response = $user_service->createUserByAdmin($request->validated(), Auth::user()->id);
      return $this->successResponse('User created', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function updateUser(UpdateUserRequest $request)
  {
    try {
      $user_service = new UserService;
      $response = $user_service->updateUser($request->validated(), Auth::user()->id);
      return $this->successResponse('User updated', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function updateProfile(UpdateProfileRequest $request)
  {
    try {
      $user_service = new UserService;
      $updated_user = $user_service->updateProfile($request->validated(), Auth::user()->id);
      return $this->successResponse('User updated', $updated_user);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function delete(DeleteUsersRequest $request)
  {
    try {
      $user_service = new UserService;
      $response     = $user_service->deleteUsers($request->ids, Auth::user()->id);
      return $this->successResponse('Users deleted', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function getAll()
  {
    try {
      $user_service = new UserService;
      $response     = $user_service->getAll();
      return $this->successResponse('Users fetched', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function getProfile()
  {
    try {
      $user_service = new UserService;
      return $this->successResponse('Users profile fetched', $user_service->getProfile(Auth::user()));
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function getUserActiveCourses()
  {
    try {
      $user_service = new UserService(
        new ContentService,
        null,
        null,
        new UserCourseScheduleService
      );
      
      $response = $user_service->getUserCourses(Auth::user()->id, StatusService::ACTIVE);
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
        new ContentService(),
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
      $user_service = new UserFavoriteService(
        new ContentService,
        new UserService
      );

      $response = $user_service->getUserFavoriteContent(Auth::user(), StatusService::ACTIVE);
      return $this->successResponse('Fetched user favorite content', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function addToFavorite(UserFavoriteRequest $request)
  {
    try {
      $user_service = new UserFavoriteService(
        new ContentService,
        new UserService()
      );

      $response = $user_service->addToFavorite($request->input('lesson_id'), Auth::user()->id);
      return $this->successResponse('Lesson has been added to the favorite list successfully', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function removeFromFavorite(UserFavoriteRequest $request)
  {
    try {
      $user_service = new UserFavoriteService(
        null,
        new UserService()
      );

      $response = $user_service->removeFromFavorite($request->input('lesson_id'), Auth::user()->id);
      return $this->successResponse('Lesson has been removed from the favorite list successfully', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function setLessonProgress(UserLessonProgressRequest $request)
  {
    try {
      $user_service = new UserService(
        new ContentService
      );
      
      $response = $user_service->setLessonProgress($request->validated(), Auth::user()->id);
      return $this->successResponse('Set the user\'s lesson progress', $response);
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function updateEmail(UpdateEmailRequest $request)
  {
    try {
      $user_service = new UserService(
        null,
        null,
        null,
        null,
        new MailService
      );
      $user_service->changeEmail(Auth::user(), $request->email);
      return $this->successResponse('An email has been sent for verification');
    } catch (Exception $ex) {
      $ex->service = self::LOG_FILE;
      return $this->errorResponse($ex);
    }
  }

  public function landedOnPageNotFound(LandedOnPageNotFoundRequest $request)
  {
    try {
      $log_service = new LogService('users');
      $log_service->error('Landed on page not found with url: ' . $request->path);
      return $this->successResponse('Registered successfully');
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function scheduleLesson(ScheduleUserCourseLessonRequest $request)
  {
    try {
      $user_course_schedule_service = new UserCourseScheduleService(
        new ContentService
      );
      $response = $user_course_schedule_service->scheduleLesson($request->id, $request->date, Auth::user()->id);
      return $this->successResponse('User course lesson has been rescheduled successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function addTrainingSchedule(AddTrainingScheduleRequest $request)
  {
    try {
      $user_course_schedule_service = new UserCourseScheduleService;
      $response = $user_course_schedule_service->addTrainingSchedule($request->validated(), Auth::user()->id);
      return $this->successResponse('User training schedule has been created successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function updateTrainingSchedule(UpdateTrainingScheduleRequest $request)
  {
    try {
      $user_course_schedule_service = new UserCourseScheduleService;
      $response = $user_course_schedule_service->updateTrainingSchedule($request->input('id'), $request->input('date'), Auth::user()->id);
      return $this->successResponse('User training schedule has been updated successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function deleteTrainingSchedule(DeleteTrainingScheduleRequest $request)
  {
    try {
      $user_course_schedule_service = new UserCourseScheduleService;
      $response = $user_course_schedule_service->deleteTrainingSchedule($request->input('id'), Auth::user()->id);
      return $this->successResponse('User training schedule has been deleted successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function getActionLogsHistory(GetActionLogsHistoryRequest $request)
  {
    try {
      $user_service = new UserService();
      $response = $user_service->GetActionLogsHistoryRequest($request->input('id'));
      return $this->successResponse('User logs has been fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function getUserChallengesHistory(GetUserChallengesRequest $request)
  {
    try {
      $user_service = new UserChallengeService(
        new ContentService()
      );
      $response = $user_service->getUserChallengesHistory($request->input('id'));
      return $this->successResponse('User challenges has been fetched successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
  
  public function submitChallenge(SubmitChallengeRequest $request)
  {
    try {
      $user_service = new UserChallengeService(
        new ContentService()
      );
      $challenge = $user_service->submitChallenge($request->validated(), Auth::user()->id);
      return $this->successResponse('Challenge has been submitted successfully', $challenge);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
  
  public function getUserChallenges()
  {
    try {
      $user_service = new UserChallengeService(
        new ContentService()
      );
      $challenge = $user_service->getUserChallenges(Auth::user()->id);
      return $this->successResponse('Challenge has been submitted successfully', $challenge);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
}