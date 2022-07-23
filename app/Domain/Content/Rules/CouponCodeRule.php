<?php

namespace App\Domain\Content\Rules;

use App\Domain\Content\Models\Coupon;
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

        if(strlen($value) !== Coupon::CODE_LENGTH) {
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
        return 'Coupon code is not valid';
    }
}
