<?php

namespace App\Domain\Content\Requests;

use App\Domain\Users\Rules\IDRule;
use App\Domain\Content\Rules\StatusRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\ContentNameRule;
use App\Domain\Content\Rules\ContentDescriptionRule;

class UpdateCourseCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'            => ['required', 'bail', new IDRule, 'exists:course_categories,id'],
            'name'          => ['required', new ContentNameRule],
            'description'   => ['nullable', new ContentDescriptionRule],
            'image'         => ['nullable', 'file', 'max:5000'],
            'status'        => ['required', new StatusRule],
        ];
    }
}
