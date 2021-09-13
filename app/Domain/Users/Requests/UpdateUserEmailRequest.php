<?php

namespace App\Domain\Users\Requests;

use App\Domain\Users\Rules\IDRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserEmailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'        => ['required', new IDRule],
            'email'     => 'required|email|unique:users,email,' . request()->id,
        ];
    }
}
