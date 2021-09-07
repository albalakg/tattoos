<?php

namespace App\Domain\Users\Requests;

use App\Rules\PasswordRule;
use App\Domain\Helpers\RulesService;
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
