<?php

namespace App\Domain\Users\Requests;

use App\Rules\IDRule;
use App\Domain\Users\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'        => ['required', 'bail', new IDRule, 'exists:users,id'],
            'password'  => ['required', 'confirmed', new PasswordRule],
        ];
    }
}
