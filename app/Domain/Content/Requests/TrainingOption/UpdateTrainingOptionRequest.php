<?php

namespace App\Domain\Content\Requests\TrainingOption;

use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\IDRule;

class UpdateTrainingOptionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'    => ['required', 'bail', new IDRule, 'exists:training_options,id'],
            'name'  => ['required', new NameRule],
        ];
    }
}
