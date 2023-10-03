<?php

namespace App\Domain\Content\Requests\CourseCategory;

use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DescriptionRule;

class CreateCourseCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'          => ['required', 'bail', new NameRule, 'unique:course_categories,name,NULL,id,deleted_at,NULL'],
            'description'   => ['nullable', new DescriptionRule],
            'image'         => ['required', 'file', 'max:10000'],
        ];
    }
}
