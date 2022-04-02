<?php

namespace App\Domain\Content\Requests;

use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DescriptionRule;

class CreateTrainerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'          => ['required', new NameRule('name')],
            'description'   => ['nullable', new DescriptionRule],
            'image'         => ['required', 'file', 'max:1000']
        ];
    }
}
