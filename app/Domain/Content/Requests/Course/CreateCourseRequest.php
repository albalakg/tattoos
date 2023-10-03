<?php

namespace App\Domain\Content\Requests\Course;

use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DescriptionRule;
use App\Rules\IDRule;

class CreateCourseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'category_id'   => ['required', 'bail', new IDRule, 'exists:course_categories,id'],
            'name'          => ['required', new NameRule],
            'description'   => ['nullable', new DescriptionRule],
            'image'         => ['required', 'file', 'max:10000'],
            'trailer'       => ['nullable', 'file', 'max:10000'],
            'price'         => 'nullable|numeric|min:1',
            'discount'      => 'nullable|numeric|min:1',
        ];
    }
}
