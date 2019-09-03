<?php

namespace App\Actions;

use App\Team;
use App\Project;

/**
 * Class CreateProject
 * @package App\Actions
 */
class CreateProject
{
    /**
     * Run the action.
     *
     * @param Team $team
     * @param array $parameters
     * @return Project
     */
    public function execute($team, $parameters)
    {
        return $team->projects()->create($parameters);
    }
}
