<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Domain\Helpers\EnvService;
use App\Domain\Helpers\LogService;
use Exception;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    const DEFAULT_ERROR = 'Sorry, we encountered an error. Please let us know';

    /**
     * Service class
     *
     * @var mixed
     */
    public $service;

    protected function successResponse(string $message, $data = null, $status = Response::HTTP_OK) : JsonResponse
    {
        return response()->json(
            [
                'message' => $message,
                'status' => true,
                'data' => $data
            ],
            $status
        );
    }
    
    /**
     * @param Exception $exception
     * @param mixed $data
     * @param int $status
     * @return JsonResponse
    */
    protected function errorResponse(Exception $exception, $data = null, $status = Response::HTTP_BAD_REQUEST) : JsonResponse
    {
        $logger = new LogService;
        $logger->error('Message: ' . $exception->getMessage() . ' | File: ' . $exception->getFile() . ' | Line: ' . $exception->getLine() . ' | ');

        $debug_error = [
            'ErrorMessage' => $exception->getMessage(),
            'File' => $exception->getFile(),
            'Line' => $exception->getLine(),
        ];

        return response()->json(
            [
                'message' => EnvService::isProd() ?  self::DEFAULT_ERROR : $debug_error,
                'status' => false,
                'data' => $data
            ],
            $status
        );
    }
}
