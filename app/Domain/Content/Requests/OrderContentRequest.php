<?php

namespace App\Domain\Content\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\IDRule;

class OrderContentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'content'                => ['required', 'array', 'between:1,1000'],
            'content.*.id'           => ['numeric', new IDRule],
            'content.*.view_order'   => ['numeric', 'between:1,1000'],
        ];
    }
}
