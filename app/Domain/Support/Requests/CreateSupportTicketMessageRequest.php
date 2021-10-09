<?php

namespace App\Domain\Support\Requests;

use App\Rules\DescriptionRule;
use App\Rules\IDRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateSupportTicketMessageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'support_ticket_id' => ['required', 'bail', new IDRule, 'exists:support_tickets,id'],
            'message'           => ['nullable', new DescriptionRule('Message')],
        ];
    }
}
