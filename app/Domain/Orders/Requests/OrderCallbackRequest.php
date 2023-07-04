<?php

namespace App\Domain\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderCallbackRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'page_request_uid'  => $this->page_request_uid,
            'approval_num'      => $this->approval_num,
            'status'            => $this->status,
        ]);
    }
    
    public function rules()
    {
        return [
            'page_request_uid'  => ['required', 'string', 'uuid'],
            'approval_num'      => ['required', 'string'],
            'status'            => ['required', 'string'],
        ];
    }
}
