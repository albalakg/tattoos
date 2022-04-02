<?php

namespace App\Domain\Content\Requests;

use App\Rules\IDRule;
use App\Rules\NameRule;
use App\Rules\DescriptionRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'            => ['required', 'bail', new IDRule, 'exists:trainers,id'],
            'name'          => ['required', new NameRule('name')],
            'description'   => ['nullable', new DescriptionRule],
            'image'         => ['required', 'file', 'max:1000']
        ];
    }
}
