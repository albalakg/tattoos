<?php

namespace App\Domain\Users\Requests;

use App\Rules\IDRule;
use App\Domain\Content\Rules\PriceRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserCourseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id'   => ['required', 'bail', new IDRule, 'exists:users,id'],
            'course_id' => ['required', 'bail', new IDRule, 'exists:courses,id'],
            'end_at'    => 'required|date',
        ];
    }
}
