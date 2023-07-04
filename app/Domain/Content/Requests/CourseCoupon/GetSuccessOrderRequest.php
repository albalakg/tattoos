<?php

namespace App\Domain\Content\Requests\CourseCoupon;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\CouponCodeRule;

class GetSuccessOrderRequest extends FormRequest
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
            'token' => ['required', 'string', 'uuid'],
        ];
    }
}
