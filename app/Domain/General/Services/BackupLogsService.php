<?php

namespace App\Domain\General\Services;

use Exception;
use App\Domain\Helpers\LogService;
use App\Domain\Helpers\FileService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BackupLogsService
{    
    const URL_PATH              = 'general/logs/backup';
    const EXCLUDED_LOGS_FILES   = ['.gitignore', 'logsBp'];

    private string $address;
    private LogService $log_service;

    public function __construct()
    {
        $this->log_service = new LogService('logsBp');
    }
    
    /**
     * Send a request to backup logs
     *
     * @param string $address
     * @param string $token
     * @return void
    */
    public function send(string $address, string $token)
    {
        $response = Http::withHeaders([
            'Authorization' => $token
        ])->post($address . self::URL_PATH);

        return json_decode($response->body());
    }
    
    /**
     * Send a request to backup logs
     *
     * @return void
    */
    public function backup()
    {
        $logs_path = $this->getAllRelevantLogsFilesPath();
        foreach($logs_path AS $log_path) {
            try {
                if(FileService::exists($log_path, FileService::S3_DISK)) {
                    FileService::delete($log_path, FileService::S3_DISK);
                }
    
                FileService::createWithName(
                    Storage::disk('logs')->get($log_path),
                    'logs',
                    FileService::getLogFileName($log_path),
                    FileService::S3_DISK
                );

                $this->log_service->info('Log backed up successfully', ['log' => $log_path]);
            } catch(Exception $ex) {
                $this->log_service->critical($ex);
            }
        }
    }

    /**
     * fetch all the relevant logs files paths, filter all the non-necessary
     *
     * @return array
    */
    private function getAllRelevantLogsFilesPath(): array
    {
        $files = Storage::disk('logs')->allFiles();
        return array_filter($files, function($file_path) {
            return !in_array($file_path, self::EXCLUDED_LOGS_FILES);
        });
    }
}