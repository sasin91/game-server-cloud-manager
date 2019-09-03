<?php

namespace App\Exceptions;

use InvalidArgumentException;

class CloudProviderNotFound extends InvalidArgumentException
{
    /**
     * Create a new exception instance
     *
     * @param string $provider
     * @return CloudProviderNotFound
     */
    public static function make($provider)
    {
        return new static("{$provider} is not supported.");
    }
}
