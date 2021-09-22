<?php

namespace App\Domain\Content\Requests;

use App\Domain\Users\Rules\IDRule;
use App\Domain\Content\Rules\StatusRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\ContentNameRule;

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
            'name'              => ['required', new ContentNameRule],
            'course_area_id'    => ['required', 'bail', new IDRule, 'exists:course_areas,id'],
            'video_id'          => ['required', 'bail', new IDRule, 'exists:videos,id'],
            'status'            => ['required', new StatusRule],
            'content'           => ['nullable', 'string'],
        ];
    }
}
