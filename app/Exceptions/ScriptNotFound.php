<?php

namespace App\Exceptions;

use InvalidArgumentException;

class ScriptNotFound extends InvalidArgumentException
{
    /**
     * Create a new exception instance
     *
     * @param string $script
     * @return ScriptNotFound
     */
    public static function make($script)
    {
        return new static("{$script} is not supported.");
    }
}
