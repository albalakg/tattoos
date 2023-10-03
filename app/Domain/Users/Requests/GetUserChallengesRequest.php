<?php

namespace App\Domain\Users\Requests;

use App\Rules\IDRule;
use Illuminate\Foundation\Http\FormRequest;

class GetUserChallengesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->id,
        ]);
    }

    public function rules()
    {
        return [
            'id' => ['required', 'bail', new IDRule, 'exists:users,id'],
        ];
    }
}
