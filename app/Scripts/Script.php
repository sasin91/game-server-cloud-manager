<?php

namespace App\Scripts;

use Illuminate\Support\Str;
use function get_class;

abstract class Script
{
    /**
     * The user that the script should be run as.
     *
     * @var string
     */
    public $sshAs = 'root';

    /**
     * Get the name of the script.
     *
     * @return string
     */
    public function name()
    {
        return Str::title(
            Str::snake(get_class($this), ' ')
        );
    }

    /**
     * Get the timeout for the script.
     *
     * @return int|null
     */
    public function timeout()
    {
        return 3600;
    }

    /**
     * Get the contents of the script.
     *
     * @return string
     */
    abstract public function script();

    /**
     * Render the script as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->script();
    }
}
