<?php

namespace App\Domain\Users\Requests;

use App\Rules\IDRule;
use App\Rules\PasswordRule;
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
            'id'        => ['required', new IDRule],
            'password'  => ['required', 'confirmed', new PasswordRule],
        ];
    }
}
