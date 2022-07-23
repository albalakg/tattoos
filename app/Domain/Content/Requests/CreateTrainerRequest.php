<?php

namespace App\Domain\Content\Requests;

use App\Rules\NameRule;
use App\Rules\DescriptionRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\TrainerTitleRule;

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
            'title'         => ['required', new TrainerTitleRule],
            'description'   => ['nullable', new DescriptionRule],
            'image'         => ['required', 'file', 'max:1000']
        ];
    }
}
