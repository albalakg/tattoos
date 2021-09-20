<?php

namespace App\Domain\Content\Requests;

use App\Domain\Content\Rules\ContentNameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\ContentDescriptionRule;

class CreateVideoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'          => ['required', new ContentNameRule],
            'description'   => ['nullable', new ContentDescriptionRule],
            'file'          => ['required', 'file', 'max:20000']
        ];
    }
}
