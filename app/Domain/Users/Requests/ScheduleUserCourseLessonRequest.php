<?php

namespace App\Domain\Users\Requests;

use App\Rules\IDRule;
use Illuminate\Foundation\Http\FormRequest;

class ScheduleUserCourseLessonRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'    => ['required', 'bail', new IDRule, 'exists:course_lessons,id'],
            'date'  => ['required', 'date'],
        ];
    }
}
