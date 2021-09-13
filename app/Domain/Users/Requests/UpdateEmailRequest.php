<?php

namespace App\Domain\Users\Requests;

use App\Domain\Users\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'     => 'required|email|unique:users,email',
            'password'  => ['required', new PasswordRule],
        ];
    }
}
