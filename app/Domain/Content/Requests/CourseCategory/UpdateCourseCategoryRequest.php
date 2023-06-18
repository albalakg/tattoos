<?php

namespace App\Domain\Content\Requests\CourseCategory;

use App\Rules\IDRule;
use App\Domain\Content\Rules\StatusRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NameRule;
use App\Rules\DescriptionRule;

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
            'name'          => ['required', 'bail', new NameRule, 'unique:course_categories,name,' . request()->id . ',id,deleted_at,NULL'],
            'description'   => ['nullable', new DescriptionRule],
            'image'         => ['nullable', 'file', 'max:50000'],
            'status'        => ['required', new StatusRule],
        ];
    }
}
