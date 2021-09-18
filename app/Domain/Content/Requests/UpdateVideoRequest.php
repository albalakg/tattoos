<?php

namespace App\Domain\Content\Requests;

use App\Domain\Content\Rules\StatusRule;
use App\Domain\Content\Rules\VideoFileRule;
use App\Domain\Content\Rules\VideoNameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\VideoDescriptionRule;
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
            'id'            => ['required', new IDRule, 'exists:videos,id'],
            'name'          => ['required', new VideoNameRule],
            'description'   => ['nullable', new VideoDescriptionRule],
            'status'        => ['required', new StatusRule],
            'file'          => ['nullable', 'file', 'max:20000']
        ];
    }
}
