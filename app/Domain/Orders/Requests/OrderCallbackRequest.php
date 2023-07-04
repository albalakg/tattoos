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
            'approval_number'   => $this->approval_number,
            'status'            => $this->status,
        ]);
    }
    
    public function rules()
    {
        return [
            'page_request_uid'  => ['required', 'string', 'uuid'],
            'approval_number'   => ['required', 'string'],
            'status'            => ['required', 'string'],
        ];
    }
}
