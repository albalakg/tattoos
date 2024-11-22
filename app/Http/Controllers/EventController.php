<?php

namespace App\Http\Controllers;

use Exception;
use App\Domain\Helpers\LogService;
use App\Services\Events\EventService;
use App\Http\Requests\UploadFileRequest;

// TODO: DELETE AFTER WEDDING
class EventController extends Controller
{
    public function find(int $event_id)
    {
        try {
            $event_service = new EventService();
            $response = $event_service->find($event_id);
            return response()->json($response);
        } catch (Exception $ex) {
            return response()->json();
        }
    }

    public function uploadFile(UploadFileRequest $request)
    {
        try {
            $event_service = new EventService();
            $response = $event_service->uploadFile($request);
            return response()->json($response);
        } catch (Exception $ex) {
            $l = new LogService('wedding');
            $l->error($ex);
            return response()->json();
        }
    }
}
