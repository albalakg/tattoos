<?php

namespace App\Domain\Users\Requests;

use App\Rules\IDRule;
use Illuminate\Foundation\Http\FormRequest;

class UserFavoriteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'lesson_id' => ['required', new IDRule('lesson')],
        ];
    }
}
