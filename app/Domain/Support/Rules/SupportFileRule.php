<?php

namespace App\Domain\Support\Rules;

use App\Domain\Helpers\FileService;
use Illuminate\Contracts\Validation\Rule;

class SupportFileRule implements Rule
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
        $valid_extensions   = ['jpg', 'bmp', 'png', 'jpeg'] ;
        $file_extension = FileService::getUploadedFileExtension($value);
        return in_array($file_extension, $valid_extensions);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Support file is not valid';
    }
}
