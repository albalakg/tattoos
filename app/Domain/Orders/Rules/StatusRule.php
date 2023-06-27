<?php

namespace App\Domain\Orders\Rules;

use App\Domain\Helpers\StatusService;
use Illuminate\Contracts\Validation\Rule;

class StatusRule implements Rule
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
        return in_array($value, StatusService::getAll());
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
