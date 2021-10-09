<?php

namespace App\Domain\Content\Requests;

use App\Domain\Content\Rules\StatusRule;
use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DescriptionRule;
use App\Rules\IDRule;

class UpdateVideoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'            => ['required', 'bail', new IDRule, 'exists:videos,id'],
            'name'          => ['required', new NameRule],
            'description'   => ['nullable', new DescriptionRule],
            'status'        => ['required', new StatusRule],
            'file'          => ['nullable', 'file', 'max:20000']
        ];
    }
}
