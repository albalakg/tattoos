<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NameRule implements Rule
{
    /**
     * @var string
    */
    private $name;
    
    /**
     * @var string
    */
    private $valid_chars= 'abcdefghijklmnopqrstuvwxyz012345679\'-_ אבגדהוזחטיכלמנסעפצקרשת';

    /**
     * Create a new rule instance.
     * 
     * @param string $name
     * @return void
     */
    public function __construct(string $name = 'ID')
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
        if(!is_string($value)) {
            return false;
        }

        $unique_chars = count_chars($value, 3);
        for($index = 0; $index < strlen($unique_chars); $index++) {
            if(!strpos($this->valid_chars, $unique_chars[$index])) {
                return false;
            }
        }

        $value_length = strlen($value);
        return $value_length >= 2 && $value_length <= 40;
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
