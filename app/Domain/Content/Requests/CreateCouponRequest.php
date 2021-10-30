<?php

namespace App\Domain\Content\Requests;

use App\Rules\IDRule;
use App\Rules\NameRule;
use App\Rules\DescriptionRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\CouponCodeRule;
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
            'code'  => ['required', new CouponCodeRule],
            'type'  => ['required', new CouponTypeRule],
            'value' => ['required', 'integer', 'between:0,10000'],
        ];
    }
}
