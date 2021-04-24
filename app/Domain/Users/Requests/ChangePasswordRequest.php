<?php

namespace App\Domain\Users\Requests;

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
            'old_password' => 'required|string|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/i',
            'password' => 'required|string|different:old_password|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/i',
        ];
    }
}
