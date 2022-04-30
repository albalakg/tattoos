<?php

namespace App\Domain\Users\Requests;

use App\Rules\IDRule;
use Illuminate\Foundation\Http\FormRequest;

class UserLessonProgressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'lesson_id'     => ['required', new IDRule('lesson')],
            'start_time'    => 'required|numeric|between:0,10000',
            'end_time'      => 'required|numeric|between:0,10000',
        ];
    }
}
