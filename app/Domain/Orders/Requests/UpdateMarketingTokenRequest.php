<?php

namespace App\Domain\Orders\Requests;

use App\Rules\IDRule;
use App\Domain\Users\Rules\PhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMarketingTokenRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'        => ['required', 'bail', new IDRule, 'exists:marketing_tokens,id'],
            'discount'  => ['required', 'string', 'between:1,10000'],
            'email'     => ['required', 'email', 'unique:marketing_tokens,email,' . request()->id],
            'name'      => ['required', 'string', 'max:120'],   
            'phone'     => ['nullable', 'bail', new PhoneRule, 'unique:marketing_tokens,phone,' . request()->id],
        ];
    }
}
