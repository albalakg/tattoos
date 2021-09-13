<?php

namespace App\Domain\Users\Requests;

use App\Domain\Users\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'     => 'required|email',
            'password'  => ['required', new PasswordRule],
        ];
    }
}
