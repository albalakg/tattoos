<?php

namespace App\Domain\Content\Requests;

use App\Domain\Content\Rules\ContentNameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\ContentDescriptionRule;
use App\Domain\Users\Rules\IDRule;

class CreateCourseAreaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'          => ['required', new ContentNameRule],
            'course_id'     => ['required', new IDRule],
            'description'   => ['nullable', new ContentDescriptionRule],
            'image'         => ['required', 'file', 'max:5000'],
            'trailer'       => ['required', 'file', 'max:10000'],
            'price'         => 'nullable|numeric|min:1',
            'discount'      => 'nullable|numeric|min:1',
        ];
    }
}
