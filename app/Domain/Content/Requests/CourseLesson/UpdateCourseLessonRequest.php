<?php

namespace App\Domain\Content\Requests\CourseLesson;

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
            'content'           => ['required', 'string',  'between:1,100000'],
            'description'       => ['required', 'string',  'between:1,100000'],
            'image'             => ['nullable', 'file', 'max:5000'],
            'skills'            => ['required', 'array','max:50'],
            'skills.*'          => [new IDRule()],
            'terms'             => ['required', 'array','max:50'],
            'terms.*'           => [new IDRule()],
            'equipment'         => ['required', 'array','max:50'],
            'equipment.*'       => [new IDRule()],
        ];
    }
}
