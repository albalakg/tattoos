<?php

namespace App\Domain\Users\Requests;

use App\Domain\Users\Rules\PhoneRule;
use App\Domain\Users\Rules\LastNameRule;
use App\Domain\Users\Rules\PasswordRule;
use App\Domain\Users\Rules\FirstNameRule;
use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'         => 'bail|required|email|unique:users,email',
            'password'      => ['required', new PasswordRule],
            'first_name'    => ['required', new FirstNameRule],
            'last_name'     => ['required', new LastNameRule],
            'phone'         => ['nullable', 'bail', new PhoneRule, 'unique:user_details,phone'],
        ];
    }
}
