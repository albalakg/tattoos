<?php

namespace App\Domain\Content\Requests;

use App\Domain\Content\Rules\VideoFileRule;
use App\Domain\Content\Rules\VideoNameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\VideoDescriptionRule;

class CreateVideoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'          => ['required', new VideoNameRule],
            'description'   => ['nullable', new VideoDescriptionRule],
            'file'          => ['required', 'file', 'max:20000']
        ];
    }
}
