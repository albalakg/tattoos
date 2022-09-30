<?php

namespace App\Domain\Content\Requests\CourseCoupon;

use App\Domain\Content\Rules\StatusRule;
use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DescriptionRule;
use App\Rules\IDRule;

class UpdateCouponStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'            => ['required', 'bail', new IDRule, 'exists:coupons,id'],
            'status'        => ['required', new StatusRule],
        ];
    }
}
