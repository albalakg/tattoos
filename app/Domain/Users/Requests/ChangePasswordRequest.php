<?php

namespace App\Domain\Users\Requests;

use App\Domain\Users\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'old_password'  => ['required', new PasswordRule],
            'password'      => ['required', 'different:old_password', new PasswordRule],
        ];
    }
}
