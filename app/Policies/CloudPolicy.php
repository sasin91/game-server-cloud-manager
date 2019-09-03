<?php

namespace App\Policies;

use App\User;
use App\Cloud;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Enums\TeamPermission;
use App\TeamMember;

class CloudPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any clouds.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the cloud.
     *
     * @param  \App\User  $user
     * @param  \App\Cloud  $cloud
     * @return mixed
     */
    public function view(User $user, Cloud $cloud)
    {
        if ($user->isOnTeam($cloud->team)) {
            return true;
        }
    }

    /**
     * Determine whether the user can create clouds.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if (blank($user->currentTeam)) {
            return false;
        }

        if ($user->isCurrentTeamOwner()) {
            return true;
        }

        if ($user->hasPermissionOnTeam(TeamPermission::CreateCloud, $user->currentTeam)) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the cloud.
     *
     * @param  \App\User  $user
     * @param  \App\Cloud  $cloud
     * @return mixed
     */
    public function update(User $user, Cloud $cloud)
    {
        if ($user->is($cloud->team->owner)) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the cloud.
     *
     * @param  \App\User  $user
     * @param  \App\Cloud  $cloud
     * @return mixed
     */
    public function delete(User $user, Cloud $cloud)
    {
        if ($user->is($cloud->team->owner)) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the cloud.
     *
     * @param  \App\User  $user
     * @param  \App\Cloud  $cloud
     * @return mixed
     */
    public function restore(User $user, Cloud $cloud)
    {
        if ($user->is($cloud->team->owner)) {
            return true;
        }
    }
}
