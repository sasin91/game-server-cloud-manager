<?php

namespace App\Scripts\AzerothCore;

use App\Contracts\AnsibleScript;

/**
 * Class DeployAzerothCoreUsingDocker
 * @package App\Scripts
 */
class DeployAzerothCoreUsingDocker implements AnsibleScript
{
    /**
     * The user that the script should be run as.
     *
     * @var string
     */
    public $sshAs = 'acore';

    /**
     * Get the contents of the script.
     *
     * @return string
     */
    public function render()
    {
        return view('scripts.AzerothCore.deploy-using-docker');
    }
}
