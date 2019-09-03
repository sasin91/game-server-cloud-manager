<?php

namespace App\Policies;

use App\Enums\TeamPermission;
use App\TeamMember;
use App\User;
use App\Server;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any servers.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->currentTeam) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the server.
     *
     * @param  \App\User  $user
     * @param  \App\Server  $server
     * @return mixed
     */
    public function view(User $user, Server $server)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->currentTeam->hasServerInAnyCloud($server)) {
            return true;
        }
    }

    /**
     * Determine whether the user can create servers.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if (is_null($user->currentTeam)) {
            return false;
        }

        if ($user->isCurrentTeamOwner()) {
            return true;
        }

        if ($user->hasPermissionOnTeam(TeamPermission::CreateServerInCloud, $user->currentTeam)) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the server.
     *
     * @param  \App\User  $user
     * @param  \App\Server  $server
     * @return mixed
     */
    public function update(User $user, Server $server)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (is_null($user->currentTeam)) {
            return false;
        }

        if ($user->currentTeam->hasServerInAnyCloud($server) === false) {
            return false;
        }

        if ($user->isCurrentTeamOwner()) {
            return true;
        }

        if ($user->hasPermissionOnTeam(TeamPermission::UpdateServerInCloud, $user->currentTeam)) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the server.
     *
     * @param  \App\User  $user
     * @param  \App\Server  $server
     * @return mixed
     */
    public function delete(User $user, Server $server)
    {
        if ($user->canDeleteServer($server)) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the server.
     *
     * @param  \App\User  $user
     * @param  \App\Server  $server
     * @return mixed
     */
    public function restore(User $user, Server $server)
    {
        if (is_null($user->currentTeam)) {
            return false;
        }

        if ($user->currentTeam->hasServerInAnyCloud($server)) {
            if ($user->isCurrentTeamOwner()) {
                return true;
            }

            return false;
        }
    }
}
