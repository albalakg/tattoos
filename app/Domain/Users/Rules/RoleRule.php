<?php

namespace App\Domain\Users\Rules;

use App\Domain\Users\Models\Role;
use Illuminate\Contracts\Validation\Rule;

class RoleRule implements Rule
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
        if(!is_string($value)) return false;
        return in_array(strtolower($value), Role::NAMES); 
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Role is not valid';
    }
}
