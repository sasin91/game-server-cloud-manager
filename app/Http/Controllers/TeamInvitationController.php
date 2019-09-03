<?php

namespace App\Http\Controllers;

use App\TeamInvitation;
use Illuminate\Http\Request;
use App\Team;
use App\Http\Resources\TeamInvitationResource;
use App\Actions\SendTeamInvitation;

class TeamInvitationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Team $team
     * @param \Illuminate\Http\Request  $request
     * @param \App\Actions\SendTeamInvitation $sendTeamInvitation
     * @return TeamInvitationResource
     */
    public function store(Team $team, Request $request, SendTeamInvitation $sendTeamInvitation)
    {
        $this->authorize('create', [new TeamInvitation, $team]);

        $validated = $this->validate($request, [
            'recipient_id' => 'required|exists:users,id'
        ]);

        return new TeamInvitationResource(
            $sendTeamInvitation->execute(
                $team,
                $validated['recipient_id'],
                $request->user()
            )
        );
    }
}
