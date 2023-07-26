<?php

namespace App\Domain\Content\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Content\Requests\DeleteRequest;
use App\Domain\Content\Services\ChallengeService;
use App\Domain\Content\Requests\Challenge\UpdateChallengeRequest;
use App\Domain\Content\Requests\Challenge\CreateChallengeRequest;
use App\Domain\Content\Requests\Challenge\UpdateChallengeStatusRequest;
use App\Domain\Users\Services\UserService;

class ChallengeController extends Controller
{
  private ChallengeService $challenge_service;
  
  public function __construct()
  {
    $this->challenge_service = new ChallengeService(
      new UserService()
    );
  }

  public function getAll()
  {
    try {
      $challenges = $this->challenge_service->getAll();
      return $this->successResponse('Challenges has been fetched successfully', $challenges);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }
  
  public function getActiveChallenge(Request $request)
  {
    try {
      $challenge = $this->challenge_service->getActiveChallenge($request->input('code'));
      unset($challenge->id);
      return $this->successResponse('Challenge has been fetched successfully', $challenge);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function update(UpdateChallengeRequest $request)
  {
    try {
      $challenge = $this->challenge_service->update($request->validated(), Auth::user()->id);
      return $this->successResponse('Challenge has been updated successfully', $challenge);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function create(CreateChallengeRequest $request)
  {
    try {
      $challenge = $this->challenge_service->create($request->validated(), Auth::user()->id);
      return $this->successResponse('Challenge has been created successfully', $challenge);
    } catch (Exception $ex) {
      return $this->errorResponse($ex);
    }
  }

  public function delete(DeleteRequest $request)
  {
    try {
      $response = $this->challenge_service->multipleDelete($request->ids, Auth::user()->id);
      return $this->successResponse('Challenges has been deleted successfully', $response);
    } catch (Exception $ex) {
      return $this->errorResponse(
        $ex,
      );
    }
  }
}