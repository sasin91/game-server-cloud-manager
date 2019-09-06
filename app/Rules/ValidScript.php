<?php

namespace App\Rules;

use App\Script;
use Illuminate\Contracts\Validation\Rule;

class ValidScript implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Script::exists($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Script is not supported.';
    }
}
