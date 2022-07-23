<?php

namespace App\Domain\Content\Rules;

use App\Domain\Content\Models\Coupon;
use Illuminate\Contracts\Validation\Rule;

class CouponTypeRule implements Rule
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
        if(!is_numeric($value)) {
            return false;
        }
        
        return in_array($value, Coupon::LIST_OF_TYPES);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Coupon type is not valid';
    }
}
