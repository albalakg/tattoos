<?php

namespace App\Domain\Content\Requests;

use App\Rules\IDRule;
use App\Rules\NameRule;
use App\Rules\DescriptionRule;
use App\Domain\Content\Rules\StatusRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\TrainerTitleRule;

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
            'title'         => ['required', new TrainerTitleRule],
            'description'   => ['nullable', new DescriptionRule],
            'status'        => ['required', new StatusRule],
            'image'         => ['nullable', 'file', 'max:1000']
        ];
    }
}
