<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NameRule implements Rule
{
    private string $name;

    private string $max_chars;
    
    /**
     * @var string
    */
    private $valid_chars= 'abcdefghijklmnopqrstuvwxyz012345679\'-_\sאבגדהוזחטיכלמנסעפצקרשת';

    /**
     * Create a new rule instance.
     * 
     * @param string $name
     * @param int $max_chars
     * @return void
     */
    public function __construct(string $name = 'Name', int $max_chars = 40)
    {
        $this->name         = $name;
        $this->max_chars    = $max_chars;
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

        $value_length = mb_strlen($value, 'UTF-8');
        return $value_length >= 2 && $value_length <= $this->max_chars;
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
