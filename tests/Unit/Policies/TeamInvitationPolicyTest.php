<?php

namespace Tests\Unit\Policies;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Policies\TeamInvitationPolicy;
use App\User;
use App\TeamInvitation;
use App\Team;
use App\TeamMember;
use App\Enums\TeamPermission;

class TeamInvitationPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new TeamInvitationPolicy;
    }

    public function test_the_team_owner_can_create_an_invitation()
    {
        $owner = factory(User::class)->create();
        $team = factory(Team::class)->create(['owner_id' => $owner->id]);

        $this->assertTrue(
            $this->policy->create(
                $owner,
                new TeamInvitation,
                $team
            ),
            "Team owner cannot create invitations"
        );
    }

    public function test_a_team_member_with_the_permission_to_send_invitations_can_create_one()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();
        TeamMember::query()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'permissions' => [TeamPermission::CreateTeamInvitations]
        ]);

        $this->assertTrue(
            $this->policy->create(
                $user,
                new TeamInvitation,
                $team
            ),
            "Team member with permission to create invites cannot create invites"
        );
    }
}
