<?php

namespace App\Domain\Users\Requests;

use App\Rules\PhoneRule;
use App\Rules\GenderRule;
use App\Rules\LastNameRule;
use App\Rules\PasswordRule;
use App\Rules\FirstNameRule;
use App\Rules\RoleRule;
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
            'role'          => ['required', new RoleRule],
            'gender'        => ['nullable', new GenderRule],
            'birth_date'    => ['nullable', 'date'],
            'phone'         => ['required', 'bail', new PhoneRule, 'unique:user_details,phone'],
            'password'      => ['required', new PasswordRule],
        ];
    }
}
