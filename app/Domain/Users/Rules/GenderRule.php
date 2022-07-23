<?php

namespace App\Domain\Users\Rules;

use App\Domain\Users\Models\UserDetail;
use Illuminate\Contracts\Validation\Rule;

class GenderRule implements Rule
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

        return in_array($value, UserDetail::GENDER_VALUES);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Gender is not valid, must be Male/Female/Other';
    }
}
