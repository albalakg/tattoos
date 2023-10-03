<?php

namespace App\Domain\Content\Requests\Equipment;

use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DescriptionRule;

class CreateEquipmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'          => ['required', new NameRule],
            'description'   => ['nullable', new DescriptionRule],
            'image'         => ['nullable', 'file', 'max:10000']
        ];
    }
}
