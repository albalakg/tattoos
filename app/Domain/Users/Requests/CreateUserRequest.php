<?php

namespace App\Domain\Users\Requests;

use App\Rules\LastNameRule;
use App\Rules\FirstNameRule;
use App\Domain\Helpers\RulesService;
use App\Rules\PasswordRule;
use App\Rules\PhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'         => 'required|email|unique:users,email',
            'first_name'    => ['required', new FirstNameRule],
            'last_name'     => ['required', new LastNameRule],
            'phone'         => ['required', 'bail', new PhoneRule, 'unique:user_details,phone'],
            'password'      => ['required', new PasswordRule],
        ];
    }
}
