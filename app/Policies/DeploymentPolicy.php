<?php

namespace App\Policies;

use App\User;
use App\Deployment;
use App\Server;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeploymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any deployments.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if ($user->currentTeam) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the deployment.
     *
     * @param  \App\User  $user
     * @param  \App\Deployment  $deployment
     * @return mixed
     */
    public function view(User $user, Deployment $deployment)
    {
        //
    }

    /**
     * Determine whether the user can create deployments.
     *
     * @param  \App\User  $user
     * @param \App\Deployment $newDeployment
     * @param \App\Server $server
     * @return mixed
     */
    public function create(User $user, Deployment $newDeployment, Server $server)
    {
        if ($user->currentTeam && $user->currentTeam->hasServerInAnyCloud($server)) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the deployment.
     *
     * @param  \App\User  $user
     * @param  \App\Deployment  $deployment
     * @return mixed
     */
    public function update(User $user, Deployment $deployment)
    {
        //
    }

    /**
     * Determine whether the user can delete the deployment.
     *
     * @param  \App\User  $user
     * @param  \App\Deployment  $deployment
     * @return mixed
     */
    public function delete(User $user, Deployment $deployment)
    {
        //
    }

    /**
     * Determine whether the user can restore the deployment.
     *
     * @param  \App\User  $user
     * @param  \App\Deployment  $deployment
     * @return mixed
     */
    public function restore(User $user, Deployment $deployment)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the deployment.
     *
     * @param  \App\User  $user
     * @param  \App\Deployment  $deployment
     * @return mixed
     */
    public function forceDelete(User $user, Deployment $deployment)
    {
        //
    }
}
