<?php

namespace App\Domain\Content\Requests\CourseLesson;

use App\Rules\IDRule;
use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\TrainingOptionValue;

class CreateCourseLessonRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'                      => ['required', new NameRule],
            'course_area_id'            => ['required', 'bail', new IDRule, 'exists:course_areas,id'],
            'video_id'                  => ['required', 'bail', new IDRule, 'exists:videos,id'],
            'image'                     => ['required', 'file', 'max:50000'],
            'content'                   => ['required', 'string',  'between:1,100000'],
            'description'               => ['required', 'string',  'between:1,100000'],
            'skills'                    => ['nullable', 'array','max:50'],
            'skills.*'                  => [new IDRule()],
            'terms'                     => ['nullable', 'array','max:50'],
            'terms.*'                   => [new IDRule()],
            'equipment'                 => ['nullable', 'array','max:50'],
            'equipment.*'               => [new IDRule()],
            'training_options.*.id'     => [new IDRule()],
            'training_options.*.value'  => [new TrainingOptionValue],
        ];
    }
}
