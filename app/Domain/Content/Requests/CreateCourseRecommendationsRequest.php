<?php

namespace App\Domain\Content\Requests;

use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DescriptionRule;
use App\Rules\IDRule;

class CreateCourseRecommendationsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'course_id'                 => ['required', 'bail', new IDRule, 'exists:courses,id'],
            'recommendations'           => 'required|array|between:1,100',
            'recommendations.*.name'    => 'required|string|between:1,100',
            'recommendations.*.content' => 'required|string|between:1,250',
        ];
    }
}
