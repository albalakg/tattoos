<?php

namespace App\Domain\Content\Requests\CourseArea;

use App\Rules\IDRule;
use App\Domain\Content\Rules\StatusRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NameRule;
use App\Rules\DescriptionRule;

class UpdateCourseAreasRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'                => ['required', 'bail', new IDRule, 'exists:course_areas,id'],
            'name'              => ['required', new NameRule],
            'course_id'         => ['required', new IDRule],
            'trainer_id'        => ['required', new IDRule],
            'description'       => ['nullable', new DescriptionRule],
            'image'             => ['nullable', 'file', 'max:50000'],
            'trailer'           => ['nullable', 'file', 'max:10000'],
            'status'            => ['required', new StatusRule],
            'lessons'           => 'nullable|array|between:0,100',
            'lessons.*'         => 'nullable|numeric|min:1|max:' . PHP_INT_MAX,
            'deleted_lessons'   => 'nullable|array|between:0,100',
            'deleted_lessons.*' => 'nullable|numeric|min:1|max:' . PHP_INT_MAX,
        ];
    }
}
