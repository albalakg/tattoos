<?php

namespace App\Domain\Content\Requests\Video;

use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DescriptionRule;

class CreateVideoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'          => ['required', new NameRule],
            'description'   => ['nullable', new DescriptionRule],
            'video_length'  => ['required', 'int', 'min:1'],
            'file'          => ['required', 'file', 'max:20000']
        ];
    }
}
