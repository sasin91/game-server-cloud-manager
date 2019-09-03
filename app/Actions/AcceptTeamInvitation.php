<?php

namespace App\Actions;

use App\TeamMember;
use App\TeamInvitation;

/**
 * Class AcceptTeamInvitation
 * @package App\Actions
 */
class AcceptTeamInvitation
{
    /**
     * Run the action.
     *
     * @param TeamInvitation $teamInvitation
     * @return TeamMember
     */
    public function execute(TeamInvitation $teamInvitation)
    {
        $teamInvitation->markAsAccepted();

        return resolve(AddTeamMember::class)->execute(
            $teamInvitation->team,
            $teamInvitation->recipient
        );
    }
}
