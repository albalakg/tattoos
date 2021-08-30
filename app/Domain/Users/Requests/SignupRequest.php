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
            'email'         => 'bail|required|email|unique:users,email',
            'password'      => 'required|string|confirmed|between:8,20',
            'first_name'    => 'required|string|between:2,50',
            'last_name'     => 'required|string|between:2,50',
            'phone'         => 'bail|nullable|string|between:8,15|unique:user_details,phone',
        ];
    }
}
