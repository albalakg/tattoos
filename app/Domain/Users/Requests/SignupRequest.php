<?php

namespace App\Domain\Users\Requests;

use App\Rules\PhoneRule;
use App\Rules\LastNameRule;
use App\Rules\PasswordRule;
use App\Rules\FirstNameRule;
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
            'phone'         => ['required', 'bail', new PhoneRule, 'unique:user_details,phone'],
        ];
    }
}
