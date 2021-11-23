<?php

namespace App\Domain\Support\Requests;

use App\Rules\IDRule;
use App\Domain\Support\Rules\SupportFileRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateSupportTicketRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'support_category_id'   => ['required', 'bail', new IDRule, 'exists:support_categories,id'],
            'title'                 => 'required|string|between:2,100',
            'description'           => 'required|string|between:2,2000',
            'file'                  => ['required', 'file', 'max:5000', 'mimes:jpg,bmp,png,jpeg', new SupportFileRule],
        ];
    }
}
