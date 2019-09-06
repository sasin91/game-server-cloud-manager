<?php

namespace App\Actions;

use App\Server;
use App\Deployment;
use App\Jobs\DeployProjectOnServer;

/**
 * Class CreateServerDeployment
 * @package App\Actions
 */
class CreateServerDeployment
{
    /**
     * Run the action.
     *
     * @param Server $server
     * @param array $attributes
     * @return Deployment
     */
    public function execute($server, $attributes)
    {
        $deployment = $server->deployments()->create($attributes);

        dispatch(new DeployProjectOnServer($deployment->project, $server));

        return $deployment;
    }
}
