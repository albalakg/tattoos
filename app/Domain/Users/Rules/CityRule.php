<?php

namespace App\Domain\Users\Rules;

use Illuminate\Contracts\Validation\Rule;

class CityRule implements Rule
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
        if(!is_string($value) && !$value) {
            return false;
        }

        $value_length = strlen($value);
        return $value_length >= 2 && $value_length <= 100;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'City is not valid';
    }
}
