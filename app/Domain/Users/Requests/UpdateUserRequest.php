<?php

namespace App\Domain\Users\Requests;

use App\Rules\IDRule;
use App\Rules\RoleRule;
use App\Rules\PhoneRule;
use App\Rules\GenderRule;
use App\Rules\StatusRule;
use App\Rules\LastNameRule;
use App\Rules\FirstNameRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'            => ['required', new IDRule],
            'first_name'    => ['required', new FirstNameRule],
            'last_name'     => ['required', new LastNameRule],
            'role'          => ['required', new RoleRule],
            'phone'         => ['required', 'bail', new PhoneRule, 'unique:user_details,phone,' . request()->id . ',user_id'],
            'status'        => ['required', new StatusRule],
            'gender'        => ['nullable', new GenderRule],
            'birth_date'    => ['nullable', 'date'],
        ];
    }
}
