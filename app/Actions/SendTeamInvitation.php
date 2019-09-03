<?php

namespace App\Actions;

use App\Team;
use App\User;
use App\TeamInvitation;
use Illuminate\Database\Eloquent\Model;
use App\TeamInvitationToken;

/**
 * Class SendTeamInvitation
 * @package App\Actions
 */
class SendTeamInvitation
{
    /**
     * Create and send an invitation to the given recipient
     *
     * @param Team $team
     * @param User|integer $recipient
     * @param User $creator
     * @return TeamInvitation|Model
     */
    public function execute($team, $recipient, $creator)
    {
        /** @var TeamInvitation $invitation */
        $invitation = $team->invitations()->create([
            'token' => TeamInvitationToken::make(),
            'recipient_id' => data_get($recipient, 'id', $recipient),
            'creator_id' => $creator->id
        ]);

        $invitation->send();

        return $invitation;
    }
}
