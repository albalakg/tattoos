<?php

namespace App\Domain\Orders\Requests;

use App\Rules\IDRule;
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
            'discount'  => ['required', 'integer', 'between:1,10000']
        ];
    }
}
