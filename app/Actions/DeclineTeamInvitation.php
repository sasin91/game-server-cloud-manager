<?php

namespace App\Actions;

use App\TeamInvitation;

/**
 * Class DeclineTeamInvitation
 * @package App\Actions
 */
class DeclineTeamInvitation
{
    /**
     * Run the action.
     *
     * @param TeamInvitation $invitation
     * @return mixed
     */
    public function execute($invitation)
    {
        $invitation->markAsDeclined();
    }
}
