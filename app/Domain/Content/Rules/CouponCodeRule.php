<?php

namespace App\Domain\Content\Rules;

use App\Domain\Helpers\StatusService;
use Illuminate\Contracts\Validation\Rule;

class CouponCodeRule implements Rule
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

        if(strlen($value) !== 10) {
            return false;
        } 
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Status is not valid';
    }
}
