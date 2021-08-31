<?php

namespace App\Domain\Users\Controllers;

use Exception;
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
  
  public function logout(UserService $user_service)
  {
    try {
      $user_service->logout(Auth::user());
      return $this->successResponse('Logged out successfully');
    } catch (Exception $ex) {
      return $this->errorResponse($ex->getMessage());
    }
  }

  public function changePassword(ChangePasswordRequest $request)
  {
    
  }

  public function getTattoos()
  {
   
  }

  public function deleteRequest()
  {
 
  }

  public function deleteResponse(DeleteAccountResponseRequest $request)
  {
    
  }

  public function updateEmail(UpdateEmailRequest $request)
  {
  
  }
}