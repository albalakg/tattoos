<?php

namespace App\Domain\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMarketingTokenRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'discount'  => ['required', 'integer', 'between:1,10000'],
            'email'     => 'required|email|unique:marketing_tokens,email'
        ];
    }
}
