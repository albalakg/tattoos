<?php

namespace App\Domain\Orders\Requests;

use App\Domain\Content\Rules\StatusRule;
use App\Rules\IDRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'        => ['required', 'bail', new IDRule, 'exists:orders,id'],
            'status'    => ['required', new StatusRule]
        ];
    }
}
