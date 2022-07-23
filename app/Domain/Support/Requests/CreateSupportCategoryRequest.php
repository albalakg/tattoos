<?php

namespace App\Domain\Support\Requests;

use App\Rules\NameRule;
use App\Rules\DescriptionRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateSupportCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'          => ['required', 'bail', new NameRule, 'unique:support_categories,name'],
            'description'   => ['nullable', new DescriptionRule],
        ];
    }
}
