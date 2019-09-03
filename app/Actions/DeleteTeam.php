<?php

namespace App\Actions;

use App\Team;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeleteTeam
 * @package App\Actions
 */
class DeleteTeam
{
    /**
     * Run the action.
     *
     * @param Team|Model $team
     * @return void
     */
    public function execute($team)
    {
        $team->delete();
    }
}
