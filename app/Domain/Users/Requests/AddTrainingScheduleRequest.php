<?php

namespace App\Domain\Users\Requests;

use App\Rules\IDRule;
use Illuminate\Foundation\Http\FormRequest;

class AddTrainingScheduleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'lesson_id' => ['required', 'bail', new IDRule, 'exists:course_lessons,id'],
            'course_id' => ['required', 'bail', new IDRule, 'exists:courses,id'],
            'date'      => ['required', 'date'],
        ];
    }
}
