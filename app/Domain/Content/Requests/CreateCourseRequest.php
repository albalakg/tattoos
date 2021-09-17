<?php

namespace App\Domain\Content\Requests;

use App\Domain\Content\Rules\VideoFileRule;
use App\Domain\Content\Rules\VideoNameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\VideoDescriptionRule;
use App\Domain\Users\Rules\IDRule;

class CreateCourseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'          => ['required', new VideoNameRule],
            'category_id'   => ['required', new IDRule],
            'description'   => ['nullable', new VideoDescriptionRule],
            'image'         => ['required', 'file', 'max:5000'],
            'trailer'       => ['required', 'file', 'max:10000'],
            'price'         => 'nullable|numeric|min:1',
            'discount'      => 'nullable|numeric|min:1',
        ];
    }
}
