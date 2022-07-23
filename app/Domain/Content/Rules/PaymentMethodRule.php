<?php

namespace App\Domain\Content\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Domain\Payment\Services\PaymentService;

class PaymentMethodRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(!is_string($value)) {
            return false;
        }

        if(!in_array($value, PaymentService::PAYMENT_METHODS)) {
            return false;
        } 

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Payment method is not valid';
    }
}
