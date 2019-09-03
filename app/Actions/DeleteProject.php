<?php

namespace App\Actions;

/**
 * Class DeleteProject
 * @package App\Actions
 */
class DeleteProject
{
    /**
     * Run the action.
     *
     * @param Project $project
     * @return void
     */
    public function execute($project)
    {
        $project->delete();
    }
}
