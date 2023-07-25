<?php

namespace App\Domain\Content\Requests\Challenge;

use App\Rules\IDRule;
use App\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateChallengeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id'            => ['required', 'bail', new IDRule(), 'exists:challenges,id'],
            'video_id'      => ['required', 'bail', new IDRule(), 'exists:videos,id'],
            'name'          => ['required', new NameRule('Name', 80)],
            'description'   => ['required', 'string', 'between:1,20000'],
            'expired_at'    => ['required', 'date'],
        ];
    }
}
