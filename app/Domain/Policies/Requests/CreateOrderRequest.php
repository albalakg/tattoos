<?php

namespace App\Domain\Orders\Requests;

use App\Rules\IDRule;
use App\Domain\Content\Rules\StatusRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\CouponCodeRule;
use App\Domain\General\Models\LuContentType;
use App\Domain\Content\Rules\PaymentMethodRule;

class CreateOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // For now only course is an available content
            'content_type_id'   => ['required', 'bail', new IDRule, 'size:' . LuContentType::COURSE],
            'content_id'        => ['required', 'bail', new IDRule],
            'coupon_code'       => ['required', 'string', new CouponCodeRule],
            'provider'          => ['required', new PaymentMethodRule]
        ];
    }
}
