<?php

namespace App;

use App\Events\TeamCreated;
use App\Events\TeamDeleted;
use App\Events\TeamRestored;
use App\Events\TeamRestoring;
use App\Events\TeamUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property User $owner
 * @property Collection $invitations
 * @property Collection $members
 * @property Collection $clouds
 */
class Team extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'owner_id'
    ];

    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
        'restoring' => TeamRestoring::class,
        'restored' => TeamRestored::class
    ];

    /**
     * Current owner of the team
     *
     * @return BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * All the invitations made for this team
     *
     * @return HasMany
     */
    public function invitations()
    {
        return $this->hasMany(TeamInvitation::class, 'team_id');
    }

    /**
     * The members of this team
     *
     * @return BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'team_members')->using(TeamMember::class)->withPivot('permissions');
    }

    /**
     * The clouds created by this team
     *
     * @return HasMany
     */
    public function clouds()
    {
        return $this->hasMany(Cloud::class);
    }

    /**
     * Whether any of this teams clouds has the given server
     *
     * @param Server $server
     * @return boolean
     */
    public function hasServerInAnyCloud(Server $server)
    {
        return $this->clouds->contains($server->cloud);
    }

    /**
     * The projects started by members of this team
     *
     * @return HasMany
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'team_id');
    }
}
