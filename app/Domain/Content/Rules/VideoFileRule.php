<?php

namespace App\Domain\Content\Rules;

use App\Domain\Helpers\StatusService;
use Illuminate\Contracts\Validation\Rule;

class VideoFileRule implements Rule
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
        // TODO: Set Video Rules
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Video file is not valid';
    }
}
