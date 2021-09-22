<?php

namespace App\Domain\Content\Requests;

use App\Domain\Content\Rules\ContentNameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\ContentDescriptionRule;

class CreateCourseCategoryRequest extends FormRequest
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
            'image'         => ['required', 'file', 'max:5000'],
        ];
    }
}
