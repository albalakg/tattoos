<?php

namespace App\Domain\Orders\Requests;

use App\Rules\IDRule;
use App\Domain\Users\Rules\PhoneRule;
use App\Domain\Orders\Rules\StatusRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateMarketingTokenRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'course_id' => ['required', 'bail', new IDRule, 'exists:courses,id'],
            'fee'       => ['required', 'integer', 'between:1,100'],
            'email'     => ['required', 'email', 'unique:marketing_tokens,email'],
            'name'      => ['required', 'string', 'max:120'],
            'phone'     => ['nullable', 'bail', new PhoneRule, 'unique:marketing_tokens,phone'],
        ];
    }
}
