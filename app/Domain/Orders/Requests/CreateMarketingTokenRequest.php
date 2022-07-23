<?php

namespace App\Domain\Orders\Requests;

use App\Domain\Users\Rules\PhoneRule;
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
            'email'     => ['required', 'email', 'unique:marketing_tokens,email'],
            'name'      => ['required', 'string', 'max:120'],
            'phone'     => ['nullable', 'bail', new PhoneRule, 'unique:marketing_tokens,phone'],
        ];
    }
}
