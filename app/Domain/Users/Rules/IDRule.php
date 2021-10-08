<?php

namespace App\Domain\Users\Rules;

use Illuminate\Contracts\Validation\Rule;

class IDRule implements Rule
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
        if(!is_numeric($value)) return false;

        return $value > 0 && $value < PHP_INT_MAX;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Last name is not valid';
    }
}