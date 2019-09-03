<?php

namespace App\Exceptions;

use InvalidArgumentException;

class VersionControlNotFound extends InvalidArgumentException
{
    /**
     * Create a new exception instance
     *
     * @param string $provider
     * @return VersionControlNotFound
     */
    public static function make($provider)
    {
        return new static("{$provider} is not supported.");
    }
}
