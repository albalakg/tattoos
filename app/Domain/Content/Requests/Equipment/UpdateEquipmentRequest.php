<?php

namespace App\Domain\Content\Requests\Equipment;

use App\Domain\Content\Rules\StatusRule;
use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DescriptionRule;
use App\Rules\IDRule;

class UpdateEquipmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'            => ['required', 'bail', new IDRule, 'exists:equipment,id'],
            'name'          => ['required', new NameRule],
            'description'   => ['nullable', new DescriptionRule],
            'status'        => ['required', new StatusRule],
            'image'         => ['nullable', 'file', 'max:10000']
        ];
    }
}
