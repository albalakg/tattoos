<?php

namespace App\Domain\Content\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Users\Rules\IDRule;

class DeleteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array|between:1,100',
            'ids.*' => [new IDRule] 
        ];
    }
}
