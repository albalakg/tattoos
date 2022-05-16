<?php

namespace App\Domain\Content\Requests;

use App\Rules\IDRule;
use App\Domain\Content\Rules\StatusRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NameRule;

class UpdateCourseLessonRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'                => ['required', 'bail', new IDRule, 'exists:course_lessons,id'],
            'name'              => ['required', new NameRule],
            'course_area_id'    => ['required', 'bail', new IDRule, 'exists:course_areas,id'],
            'video_id'          => ['required', 'bail', new IDRule, 'exists:videos,id'],
            'status'            => ['required', new StatusRule],
            'image'             => ['nullable', 'file', 'max:5000'],
            'rehearsals'        => ['nullable', 'numeric', 'between:1,100000'],
            'rest_time'         => ['nullable', 'numeric', 'between:1,100000'],
            'activity_time'     => ['nullable', 'numeric', 'between:1,100000'],
            'activity_period'   => ['nullable', 'numeric', 'between:1,100'],
            'content'           => ['nullable', 'string'],
        ];
    }
}
