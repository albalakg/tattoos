<?php

namespace App\Domain\Support\Requests;

use App\Domain\Content\Rules\StatusRule;
use App\Rules\IDRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSupportCategoryStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'        => ['required', 'bail', new IDRule],
            'status'    => ['required', new StatusRule]
        ];
    }
}
