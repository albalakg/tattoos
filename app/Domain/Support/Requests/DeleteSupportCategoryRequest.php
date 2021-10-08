<?php

namespace App\Domain\Support\Requests;

use App\Domain\Users\Rules\IDRule;
use Illuminate\Foundation\Http\FormRequest;

class DeleteSupportCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required', 'bail', new IDRule, 'exists:support_categories,id'],
        ];
    }
}
