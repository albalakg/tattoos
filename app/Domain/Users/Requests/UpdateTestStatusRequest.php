<?php

namespace App\Domain\Users\Requests;

use App\Domain\Users\Rules\IDRule;
use App\Domain\Users\Rules\StatusRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTestStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'        => ['required', 'bail', new IDRule, 'exists:user_course_submissions,id'],
            'status'    => ['required', new StatusRule],
        ];
    }
}
