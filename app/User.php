<?php

namespace App;

use App\Concerns\HasTeams;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property Collection $clouds
 * @property Collection $teamInvitations
 * @property Collection $sentTeamInvitations
 * @property Team|null $currentTeam
 * @property Collection $teams
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasTeams;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'team_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean'
    ];

    /**
     * Whether the user is an admin
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return (bool)$this->is_admin;
    }

    /**
     * The clouds deployed by the current users team
     *
     * @return HasManyThrough
     */
    public function clouds()
    {
        return $this->hasManyThrough(Cloud::class, Team::class, 'id', 'team_id', 'team_id');
    }
}
