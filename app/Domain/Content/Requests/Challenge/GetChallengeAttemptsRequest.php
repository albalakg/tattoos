<?php

namespace App\Domain\Content\Requests\Challenge;

use App\Rules\IDRule;
use App\Rules\NameRule;
use App\Domain\Content\Rules\StatusRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\TrainingOptionValue;

class GetChallengeAttemptsRequest extends FormRequest
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
            'id' => ['required', 'bail', new IDRule(), 'exists:challenges,id'],
        ];
    }
}
