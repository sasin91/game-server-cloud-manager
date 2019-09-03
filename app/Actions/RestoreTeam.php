<?php

namespace App\Actions;

use App\Team;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RestoreTeam
 * @package App\Actions
 */
class RestoreTeam
{
    /**
     * Run the action.
     *
     * @param mixed|Team $team
     * @return Team|null|Model
     */
    public function execute($team)
    {
        if ($team->restore()) {
            return $team;
        }
    }
}
