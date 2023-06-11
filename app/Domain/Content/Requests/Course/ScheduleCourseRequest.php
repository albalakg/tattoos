<?php

namespace App\Domain\Content\Requests\Course;

use App\Rules\IDRule;
use Illuminate\Foundation\Http\FormRequest;

class ScheduleCourseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'                    => ['required', 'bail', new IDRule, 'exists:courses,id'],
            'lessonsId'             => ['required', 'array', 'between:1, 1000'],
            'lessonsId.*.id'        => ['required', new IDRule],
            'lessonsId.*.date'      => ['required', 'date'],
            'lessonsId.*.type_id'   => ['required', 'int', 'in:1,2'],
        ];
    }
}