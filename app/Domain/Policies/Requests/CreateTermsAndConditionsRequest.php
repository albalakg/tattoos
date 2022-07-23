<?php

namespace App\Domain\Policies\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTermsAndConditionsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'content'  => ['required', 'string', 'between:1,100000'],
        ];
    }
}
