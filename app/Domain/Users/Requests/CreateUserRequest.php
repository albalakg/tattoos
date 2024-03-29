<?php

namespace App\Domain\Users\Requests;

use App\Domain\Users\Rules\CityRule;
use App\Domain\Users\Rules\RoleRule;
use App\Domain\Users\Rules\TeamRule;
use App\Domain\Users\Rules\PhoneRule;
use App\Domain\Users\Rules\GenderRule;
use App\Domain\Users\Rules\LastNameRule;
use App\Domain\Users\Rules\PasswordRule;
use App\Domain\Users\Rules\FirstNameRule;
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
            'team'          => ['nullable', new TeamRule],
            'city'          => ['nullable', new CityRule],
            'is_subscribed' => ['required', 'boolean'],
            'birth_date'    => ['nullable', 'date'],
            'phone'         => ['required', 'bail', new PhoneRule, 'unique:user_details,phone'],
            'password'      => ['required', new PasswordRule],
        ];
    }
}
