<?php

namespace App\Actions;

use App\Team;
use App\User;
use App\TeamMember;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CreateTeam
 * @package App\Actions
 */
class CreateTeam
{
    /**
     * Run the action.
     *
     * @param string $name
     * @param User $owner
     * @return Team|Model
     */
    public function execute($name, $owner)
    {
        $team = Team::query()->create([
            'owner_id' => $owner->id,
            'name' => $name
        ]);

        $membership = resolve(AddTeamMember::class)->execute(
            $team,
            $owner
        );

        $team->setRelation('members', collect([
            $membership
        ]));

        return $team;
    }
}
