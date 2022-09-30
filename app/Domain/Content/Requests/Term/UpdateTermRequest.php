<?php

namespace App\Domain\Content\Requests\Term;

use App\Domain\Content\Rules\StatusRule;
use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DescriptionRule;
use App\Rules\IDRule;

class UpdateTermRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'            => ['required', 'bail', new IDRule, 'exists:terms,id'],
            'name'          => ['required', new NameRule],
            'description'   => ['nullable', new DescriptionRule],
            'status'        => ['required', new StatusRule],
            'image'         => ['nullable', 'file', 'max:5000']
        ];
    }
}
