<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:jpeg,png,jpg,gif,mp4,mov,avi,webp',
                'max:10240',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif,video/mp4,video/quicktime,video/x-msvideo',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'A file must be uploaded.',
            'file.mimes' => 'The file must be an image (jpeg, png, jpg, gif, webp) or a video (mp4, mov, avi).',
            'file.max' => 'The file size must not exceed 10MB.',
            'file.mimetypes' => 'The file type is not supported. Only valid images or videos are allowed.',
        ];
    }
}
