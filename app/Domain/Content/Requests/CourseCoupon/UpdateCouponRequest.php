<?php

namespace App\Domain\Content\Requests\CourseCoupon;

use App\Rules\IDRule;
use App\Domain\Content\Rules\StatusRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\CouponTypeRule;

class UpdateCouponRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'        => ['required', 'bail', new IDRule, 'exists:coupons,id'],
            'type'      => ['required', new CouponTypeRule],
            'value'     => ['required', 'integer', 'between:0,10000'],
            'status'    => ['required', new StatusRule],
        ];
    }
}
