<?php

namespace App\Domain\Users\Requests;

use App\Domain\Users\Rules\IDRule;
use Illuminate\Foundation\Http\FormRequest;

class DeleteUsersRequest extends FormRequest
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
