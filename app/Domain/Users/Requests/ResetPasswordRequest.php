<?php

namespace App\Domain\Users\Requests;

use App\Rules\PasswordRule;
use App\Domain\Helpers\RulesService;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'     => 'required|email',
            'token'     => 'required|string|size:50',
            'password'  => ['required', new PasswordRule],
        ];
    }
}
