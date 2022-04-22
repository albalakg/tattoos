<?php

namespace App\Domain\Users\Requests;

use App\Domain\Users\Rules\PhoneRule;
use App\Domain\Users\Rules\GenderRule;
use App\Domain\Users\Rules\LastNameRule;
use App\Domain\Users\Rules\FirstNameRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name'    => ['required', new FirstNameRule],
            'last_name'     => ['required', new LastNameRule],
            'phone'         => ['nullable', 'bail', new PhoneRule],
            'gender'        => ['nullable', new GenderRule],
            'birth_date'    => ['nullable', 'date'],
        ];
    }
}
