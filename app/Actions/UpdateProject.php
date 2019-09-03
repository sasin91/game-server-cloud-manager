<?php

namespace App\Actions;

use App\Project;

/**
 * Class UpdateProject
 * @package App\Actions
 */
class UpdateProject
{
    /**
     * Run the action.
     *
     * @param Project $project
     * @param array $parameters
     * @return Project
     */
    public function execute($project, $parameters)
    {
        $project->update(
            $parameters
        );

        return $project;
    }
}
