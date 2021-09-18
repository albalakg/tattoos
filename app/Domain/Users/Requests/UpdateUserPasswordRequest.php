<?php

namespace App\Domain\Users\Requests;

use App\Domain\Users\Rules\IDRule;
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
            'id'        => ['required', new IDRule, 'exists:users,id'],
            'password'  => ['required', 'confirmed', new PasswordRule],
        ];
    }
}
