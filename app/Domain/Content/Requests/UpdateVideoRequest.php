<?php

namespace App\Domain\Content\Requests;

use App\Domain\Content\Rules\StatusRule;
use App\Domain\Content\Rules\ContentNameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\ContentDescriptionRule;
use App\Domain\Users\Rules\IDRule;

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
            'name'          => ['required', new ContentNameRule],
            'description'   => ['nullable', new ContentDescriptionRule],
            'status'        => ['required', new StatusRule],
            'file'          => ['nullable', 'file', 'max:20000']
        ];
    }
}
