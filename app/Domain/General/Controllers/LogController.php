<?php

namespace App\Domain\General\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use App\Domain\General\Requests\BackupLogsRequest;
use App\Domain\General\Services\BackupLogsService;

class LogController extends Controller
{  
  public function backup()
  {
    try {
        $service = new BackupLogsService();
        $response = $service->backup();
        return $this->successResponse('Backed up all the logs successfully', $response);
    } catch (Exception $ex) {
        return $this->errorResponse($ex);
    }
  }
}