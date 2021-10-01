<?php

namespace App\Domain\Users\Requests;

use App\Domain\Users\Rules\CommentRule;
use App\Domain\Users\Rules\IDRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateTestCommentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_course_submission_id'     => ['required', 'bail', new IDRule, 'exists:user_course_submission_comments,id'],
            'comment'                       => ['required', new CommentRule],
        ];
    }
}
