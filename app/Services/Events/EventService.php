<?php

namespace App\Services\Events;

use App\Models\Event;
use App\Services\Helpers\FileService;
use App\Http\Requests\UploadFileRequest;
use App\Http\Requests\CreateEventRequest;

class EventService
{
    /**
     * @param int $event_id
     * @return ?array
    */
    public function find(int $event_id): ?array
    {
        $files = FileService::getAllFilesInFolder('wedding', 's3');
        if(!$files) {
            return [];
        }

        // Filter and determine type of each file
        $filteredFiles = array_map(function($file) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $type = in_array(strtolower($extension), ['mp4', 'mov', 'avi', 'mkv', 'flv', 'wmv']) ? 'video' : 'image';
            return [
                'path' => $file,
                'type' => $type
            ];
        }, $files);

        return array_values($filteredFiles);
        // return Event::where('id', $event_id)
        //     ->with('assets')
        //     ->first();
    }

    /**
     * @param UploadFileRequest $request
     * @return array
     */
    public function uploadFile(UploadFileRequest $request): array
    {
        return [ 'path' => FileService::create($request['file'], 'wedding', 's3') ];
        // $event_asset->path = FileService::create($request['file'], 'files', FileService::S3_DISK);
        // $event_asset = new EventAsset;
        // $event_asset->event_id = $request->event_id;
        // $event_asset->save();

        // return $event_asset;
    }
}
