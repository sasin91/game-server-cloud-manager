<?php

namespace App\Actions;

use App\Team;
use App\Cloud;
use App\Environment;
use Illuminate\Support\Arr;

/**
 * Class CreateCloud
 * @package App\Actions
 */
class CreateCloud
{
    /**
     * Run the action.
     *
     * @param Team $team
     * @param array $parameters
     * @return Cloud
     */
    public function execute(Team $team, array $parameters)
    {
        if (isset($parameters['environment'])) {
            $environment = Environment::query()->create([
                'variables' => Arr::pull($parameters, 'environment', [])
            ]);
        } else {
            $environment = Environment::query()->find(
                $parameters['environment_id']
            );
        }

        return tap($team->clouds()->create(
            $parameters + ['environment_id' => $environment->getKey()]
        ), function (Cloud $cloud) use ($environment) {
            $cloud->environment()->associate($environment);
        });
    }
}
