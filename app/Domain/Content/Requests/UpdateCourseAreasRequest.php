<?php

namespace App\Domain\Content\Requests;

use App\Domain\Users\Rules\IDRule;
use App\Domain\Content\Rules\StatusRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NameRule;
use App\Rules\DescriptionRule;

class UpdateCourseAreasRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'            => ['required', 'bail', new IDRule, 'exists:course_areas,id'],
            'name'          => ['required', new NameRule],
            'course_id'     => ['required', new IDRule],
            'description'   => ['nullable', new DescriptionRule],
            'image'         => ['nullable', 'file', 'max:5000'],
            'trailer'       => ['nullable', 'file', 'max:10000'],
            'status'        => ['required', new StatusRule],
        ];
    }
}
