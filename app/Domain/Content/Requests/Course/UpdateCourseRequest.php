<?php

namespace App\Domain\Content\Requests\Course;

use App\Rules\IDRule;
use App\Domain\Content\Rules\StatusRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NameRule;
use App\Rules\DescriptionRule;

class UpdateCourseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'            => ['required', 'bail', new IDRule, 'exists:courses,id'],
            'category_id'   => ['required', 'bail', new IDRule, 'exists:course_categories,id'],
            'name'          => ['required', new NameRule],
            'description'   => ['nullable', new DescriptionRule],
            'image'         => ['nullable', 'file', 'max:5000'],
            'trailer'       => ['nullable', 'file', 'max:10000'],
            'price'         => 'nullable|numeric|min:1',
            'discount'      => 'nullable|numeric|min:1',
            'status'        => ['required', new StatusRule],
        ];
    }
}
