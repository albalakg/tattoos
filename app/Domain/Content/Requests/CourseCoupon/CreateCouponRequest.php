<?php

namespace App\Domain\Content\Requests\CourseCoupon;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\CouponTypeRule;

class CreateCouponRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type'  => ['required', new CouponTypeRule],
            'value' => ['required', 'integer', 'between:0,10000'],
        ];
    }
}
