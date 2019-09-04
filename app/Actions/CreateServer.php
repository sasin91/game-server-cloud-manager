<?php

namespace App\Actions;

use App\Cloud;
use App\Server;
use App\Environment;
use App\Jobs\CreateServerInCloud;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CreateServer
 * @package App\Actions
 */
class CreateServer
{
    /**
     * Run the action.
     *
     * @param Cloud $cloud
     * @param array $parameters
     * @return Server
     */
    public function execute(Cloud $cloud, array $parameters)
    {
        if (isset($parameters['environment'])) {
            $environment = Environment::query()->create([
                'variables' => Arr::wrap(
                    Arr::pull($parameters, 'environment', [])
                )
            ]);
        } else {
            $environment = Environment::query()->find(
                $parameters['environment_id']
            );
        }

        return tap($cloud->servers()->create(
            $parameters + ['environment_id' => $environment->getKey()]
        ), function (Server $server) use ($environment) {
            $server->environment()->associate($environment);

            dispatch(new CreateServerInCloud($server));
        });
    }
}
