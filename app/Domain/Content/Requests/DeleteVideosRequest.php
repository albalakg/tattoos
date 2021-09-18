<?php

namespace App\Domain\Content\Requests;

use App\Domain\Content\Rules\StatusRule;
use App\Domain\Content\Rules\VideoFileRule;
use App\Domain\Content\Rules\VideoNameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\VideoDescriptionRule;
use App\Domain\Users\Rules\IDRule;

class DeleteVideosRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array|between:1,100',
            'ids.*' => [new IDRule] 
        ];
    }
}
