<?php

namespace App\Domain\Orders\Requests;

use App\Domain\Orders\Models\MarketingToken;
use Illuminate\Foundation\Http\FormRequest;

class GetMarketingTokenRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'token' => $this->token,
        ]);
    }

    public function rules()
    {
        return [
            'token' => 'required|string|size:' . MarketingToken::TOKEN_LENGTH,
        ];
    }
}
