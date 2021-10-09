<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DescriptionRule implements Rule
{    
    /**
     * @var string
    */
    private $name;
    
    /**
     * Create a new rule instance.
     *
     * @param string $name
     * @return void
     */
    public function __construct(string $name = 'Description')
    {
        $this->name = $name;
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

        $value_length = strlen($value);
        return $value_length >= 1 && $value_length <= 5000;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->name . ' is not valid';
    }
}
