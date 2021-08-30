<?php

namespace App\Http\Controllers;

use App\Domain\Helpers\EnvService;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
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

    protected function errorResponse(string $message, $data = null, $status = Response::HTTP_BAD_REQUEST) : JsonResponse
    {
        return response()->json(
            [
                'message' => EnvService::isNotProd() ?  $message : self::DEFAULT_ERROR,
                'status' => false,
                'data' => $data
            ],
            $status
        );
    }
}
