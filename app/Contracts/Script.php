<?php

namespace App\Contracts;

interface Script
{
    /**
     * Get the contents of the script.
     *
     * @return string
     */
    public function render();
}
