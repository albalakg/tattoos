<?php

namespace App\Domain\Content\Requests;

use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\IDRule;

class CreateCourseLessonRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'              => ['required', new NameRule],
            'course_area_id'    => ['required', 'bail', new IDRule, 'exists:course_areas,id'],
            'video_id'          => ['required', 'bail', new IDRule, 'exists:videos,id'],
            'image'             => ['required', 'file', 'max:5000'],
            'content'           => ['required', 'string',  'between:1,100000'],
            'description'       => ['required', 'string',  'between:1,100000'],
            'rehearsals'        => ['nullable', 'numeric', 'between:1,100000'],
            'rest_time'         => ['nullable', 'numeric', 'between:1,100000'],
            'activity_time'     => ['nullable', 'numeric', 'between:1,100000'],
            'activity_period'   => ['nullable', 'numeric', 'between:1,100'],
        ];
    }
}
