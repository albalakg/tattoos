<?php

namespace App\Domain\Content\Requests\Challenge;

use App\Rules\IDRule;
use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\TrainingOptionValue;

class SubmitChallengeRequest extends FormRequest
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
            'id'        => ['required', 'bail', new IDRule, 'exists:challenges,id'],
            'is_public' => ['required', 'numeric', 'in:0,1'],
            'video'     => ['required', 'mimetypes:video/x-ms-asf,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/avi', 'max:20000'],
        ];
    }
}
