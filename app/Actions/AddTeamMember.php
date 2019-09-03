<?php

namespace App\Actions;

use App\Team;
use App\User;
use App\TeamMember;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AddTeamMember
 * @package App\Actions
 */
class AddTeamMember
{
    /**
     * Run the action.
     *
     * @param Team $team
     * @param User $user
     * @param array $permissions
     * @return TeamMember|Model
     */
    public function execute($team, $user, $permissions = [])
    {
        return TeamMember::query()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'permissions' => $permissions
        ]);
    }
}
