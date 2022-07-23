<?php

namespace App\Domain\Policies\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyTermsAndConditionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tnc_id'  => 'required|int|min:1|bail|exists:policies_terms_and_conditions.id',
        ];
    }
}
