<?php

namespace App\Domain\Users\Requests;

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
            'email' => 'bail|required|email|unique:users,email',
            'password' => 'required|string|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,20}$/i',
            'first_name' => 'required|string|between:2,50',
            'last_name' => 'required|string|between:2,50',
            'phone' => 'bail|nullable|string|between:8,15|unique:users,phone',
        ];
    }

    public function messages()
    {
        return [
            'password.regex' => 'Password must be between 8-20 chars and must contains numbers and letters'
        ];
    }
}
