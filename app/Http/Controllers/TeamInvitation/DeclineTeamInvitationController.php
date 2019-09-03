<?php

namespace App\Http\Controllers\TeamInvitation;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Actions\DeclineTeamInvitation;

class DeclineTeamInvitationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param DeclineTeamInvitation $declineTeamInvitation
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, DeclineTeamInvitation $declineTeamInvitation)
    {
        $validated = $this->validate($request, [
            'token' => ['required', 'exists:team_invitations,token']
        ]);

        /** @var User $user */
        $user = $request->user();

        $invitation = $user
            ->teamInvitations()
            ->onlyEligable()
            ->where('token', $validated['token'])
            ->firstOrFail();

        $declineTeamInvitation->execute($invitation);
    }
}
