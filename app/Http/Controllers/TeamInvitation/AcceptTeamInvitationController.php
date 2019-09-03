<?php

namespace App\Http\Controllers\TeamInvitation;

use App\User;
use App\TeamInvitation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Actions\AcceptTeamInvitation;

class AcceptTeamInvitationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Actions\AcceptTeamInvitation $acceptTeamInvitation
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, AcceptTeamInvitation $acceptTeamInvitation)
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

        $acceptTeamInvitation->execute($invitation);
    }
}
