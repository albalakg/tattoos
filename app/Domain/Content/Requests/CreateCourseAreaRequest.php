<?php

namespace App\Domain\Content\Requests;

use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DescriptionRule;
use App\Rules\IDRule;

class CreateCourseAreaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'          => ['required', new NameRule],
            'course_id'     => ['required', new IDRule],
            'description'   => ['nullable', new DescriptionRule],
            'image'         => ['required', 'file', 'max:5000'],
            'trailer'       => ['required', 'file', 'max:10000'],
            'price'         => 'nullable|numeric|min:1',
            'discount'      => 'nullable|numeric|min:1',
            'lessons'       => 'required|array|between:0,100',
            'lessons.*'     => 'nullable|numeric|min:1|max:' . PHP_INT_MAX
        ];
    }
}
