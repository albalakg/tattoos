<?php

namespace App\Domain\Content\Requests\TrainingOption;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\TrainingOptionNameRule;

class CreateTrainingOptionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', new TrainingOptionNameRule],
        ];
    }
}
