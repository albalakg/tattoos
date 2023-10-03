<?php

namespace App\Domain\Content\Requests\Challenge;

use App\Rules\IDRule;
use App\Rules\NameRule;
use App\Domain\Content\Rules\StatusRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Content\Rules\TrainingOptionValue;

class UpdateChallengeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'                => ['required', 'bail', new IDRule(), 'exists:challenges,id'],
            'video_id'          => ['required', 'bail', new IDRule(), 'exists:videos,id'],
            'name'              => ['required', new NameRule('Name', 80), 'unique:challenges,name,' . request()->id . ',id,deleted_at,NULL'],
            'status'            => ['required', new StatusRule],
            'description'       => ['required', 'string', 'between:1,20000'],
            'image'             => ['nullable', 'file', 'max:10000'],
            'expired_at'        => ['required', 'date'],
            'options.*.id'      => [new IDRule()],
            'options.*.value'   => [new TrainingOptionValue],
        ];
    }
}
