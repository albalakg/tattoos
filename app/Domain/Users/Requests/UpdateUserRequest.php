<?php

namespace App\Domain\Users\Requests;

use App\Rules\IDRule;
use App\Domain\Users\Rules\RoleRule;
use App\Domain\Users\Rules\PhoneRule;
use App\Domain\Users\Rules\GenderRule;
use App\Domain\Users\Rules\StatusRule;
use App\Domain\Users\Rules\LastNameRule;
use App\Domain\Users\Rules\FirstNameRule;
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
            'id'            => ['required', 'bail', new IDRule, 'exists:users,id'],
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
