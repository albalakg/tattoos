<?php

namespace App\Domain\Content\Requests;

use App\Domain\Content\Rules\StatusRule;
use App\Domain\Users\Rules\PasswordRule;
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
            'description'   => ['required', new VideoDescriptionRule],
            'status'        => ['required', new StatusRule],
            'file'          => ['required', 'file', 'max:20000', new VideoFileRule]
        ];
    }
}
