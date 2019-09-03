<?php

namespace App\Policies;

use App\User;
use App\TeamInvitation;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Enums\TeamPermission;
use App\Team;

class TeamInvitationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create team invitations.
     *
     * @param \App\User $user
     * @param \App\TeamInvitation $_pendingTeamInvitation
     * @param \App\Team $team
     * @return mixed
     */
    public function create(User $user, TeamInvitation $_pendingTeamInvitation, Team $team)
    {
        if ($user->is($team->owner)) {
            return true;
        }

        if ($user->isOnTeam($team)) {
            return $user->hasPermissionOnTeam(
                TeamPermission::CreateTeamInvitations,
                $team
            );
        }
    }
}
