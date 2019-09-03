<?php

namespace App\Concerns;

use App\Team;
use App\Server;
use App\TeamMember;
use App\TeamInvitation;
use App\Enums\TeamPermission;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasTeams
{
    /**
     * The team invitations this user has received
     *
     * @return HasMany|TeamInvitation
     */
    public function teamInvitations()
    {
        return $this->hasMany(TeamInvitation::class, 'recipient_id');
    }

    /**
     * The team invitations created by this user
     *
     * @return HasMany
     */
    public function sentTeamInvitations()
    {
        return $this->hasMany(TeamInvitation::class, 'creator_id');
    }

    /**
     * The users current team
     *
     * @return BelongsTo
     */
    public function currentTeam()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * Accessor for currentTeam attribute
     *
     * @return Team
     */
    public function getCurrentTeamAttribute()
    {
        /** @var Team|null $team */
        $team = $this->getRelationValue('currentTeam') ?? $this->currentTeam()->getResults();

        if ($team && $team->relationLoaded('membership') === false) {
            $team->setRelation('membership', TeamMember::query()->where([
                'team_id' => $team->id,
                'user_id' => $this->id,
            ])->first());
        }

        return $team;
    }

    /**
     * All the users teams
     *
     * @return BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members')->using(TeamMember::class)->withPivot('permissions');
    }

    /**
     * Check whether the user is on given team
     *
     * @param Team $team
     * @return boolean
     */
    public function isOnTeam($team)
    {
        return $this->teams->contains($team);
    }

    /**
     * Find team by id
     *
     * @param id|object $team
     * @return Team|null
     */
    public function findTeam($team)
    {
        return $this->teams->where('team_id', data_get($team, 'id', $team))->first();
    }

    /**
     * Check whether the user is has given permission on given team
     *
     * @param TeamPermission|string $permission
     * @param Team|null $team
     * @return boolean
     */
    public function hasPermissionOnTeam($permission, $team = null)
    {
        $permission = (string)$permission;
        $team = $team ?? $this->currentTeam;

        if (is_null($team)) {
            return false;
        }

        $membership = $team->membership ?? TeamMember::query()->where([
            'team_id' => $team->id,
            'user_id' => $this->id,
        ])->first();

        if ($membership !== null) {
            return in_array($permission, $membership->permissions, true);
        }

        return false;
    }

    /**
     * Whether this user owns the current team
     *
     * @return boolean
     */
    public function isCurrentTeamOwner()
    {
        if (is_null($this->currentTeam)) {
            return false;
        }

        return $this->is(
            $this->currentTeam->owner
        );
    }

    /**
     * Whether the user can delete given server
     *
     * @param Server $server
     * @return boolean
     */
    public function canDeleteServer(Server $server)
    {
        if ($this->isCurrentTeamOwner()) {
            return $this->currentTeam->hasServerInAnyCloud($server);
        }

        return $this->teams->first(function (Team $team) use ($server) {
            if ($this->is($team->owner) || $this->hasPermissionOnTeam(TeamPermission::DeleteServerInCloud, $team)) {
                return $team->hasServerInAnyCloud($server);
            }
        }) !== null;
    }
}
