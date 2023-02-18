<?php

namespace App\Domain\Content\Requests\Course;

use App\Rules\IDRule;
use App\Domain\Content\Rules\StatusRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NameRule;
use App\Rules\DescriptionRule;

class ScheduleCourseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'                => ['required', 'bail', new IDRule, 'exists:courses,id'],
            'lessonsId'         => ['required', 'array', 'between:1, 1000'],
            'lessonsId.*.id'    => ['required', new IDRule],
            'lessonsId.*.date'  => ['required', 'date'],
        ];
    }
}
