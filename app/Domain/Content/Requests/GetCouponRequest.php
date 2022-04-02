<?php

namespace App\Domain\Content\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\CouponCodeRule;

class GetCouponRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code' => ['required', 'bail', new CouponCodeRule]
        ];
    }
}
