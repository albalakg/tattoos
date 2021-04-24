<?php

namespace App\Domain\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAccountResponseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'token' => 'required|string|size:50',
            'status' => 'required|numeric|in:0,1',
        ];
    }
}
