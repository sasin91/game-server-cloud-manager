<?php

namespace App\Actions;

use App\Project;

/**
 * Class RestoreProject
 * @package App\Actions
 */
class RestoreProject
{
    /**
     * Run the action.
     *
     * @param Project $project
     * @return Project
     */
    public function execute($project)
    {
        $project->restore();

        return $project;
    }
}
