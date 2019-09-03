<?php

namespace App\Contracts;

interface Deployable
{
    /**
     * Get the ID used by the server provider
     *
     * @return string
     */
    public function getProviderId();
}
