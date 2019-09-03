<?php

namespace App\Actions;

use App\Team;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UpdateTeam
 * @package App\Actions
 */
class UpdateTeam
{
    /**
     * Run the action.
     *
     * @param Team|Model $team
     * @param array $parameters
     * @return Team|Model
     */
    public function execute($team, array $parameters)
    {
        $team->update($parameters);

        return $team;
    }
}
