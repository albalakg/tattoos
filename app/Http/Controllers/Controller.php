<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Domain\Helpers\EnvService;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    const DEFAULT_ERROR = 'Sorry, we encountered an error. Please let us know';

    protected string $log_channel = LogService::DEFAULT_CHANNEL;

    /**
     * Service class
     *
     * @var mixed
     */
    public $service;

    /**
     * Log file
     *
     * @var string
     */
    public $log_file;
    
    /**
     * @param string $message
     * @param mixed $data
     * @param int $status
     * @param string $log_message
     * @return JsonResponse
    */
    protected function successResponse(string $message, $data = null, int $status = Response::HTTP_OK, string $log_message = '') : JsonResponse
    {
        if($log_message) {
            $logger = new LogService($this->log_file ?? LogService::DEFAULT_CHANNEL, Auth::user());
            $logger->info($log_message);
        }

        return response()->json(
            [
                'message'   => $message,
                'status'    => true,
                'data'      => $data
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
        $logger = new LogService($this->log_channel, Auth::user());
        $logger->error($exception);

        $debug_error = [
            'ErrorMessage'  => $exception->getMessage(),
            'File'          => $exception->getFile(),
            'Line'          => $exception->getLine(),
        ];
        
        $error_data = [
            'message'   => EnvService::isNotProd() ? $debug_error['ErrorMessage'] : self::DEFAULT_ERROR,
            'status'    => false,
            'data'      => $data
        ];

        if(EnvService::isNotProd()) {
            $error_data['debug_info'] = $debug_error;
        }

        return response()->json(
            $error_data,
            $status ? $status : Response::HTTP_BAD_REQUEST
        );
    }
}
