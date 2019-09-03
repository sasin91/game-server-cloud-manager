<?php

namespace Tests\Unit\Policies;

use App\User;
use App\Cloud;
use Tests\TestCase;
use App\Policies\CloudPolicy;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Team;
use App\Actions\AddTeamMember;
use App\Enums\TeamPermission;
use App\TeamMember;

class CloudPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new CloudPolicy;
    }

    public function test_admin_can_view_any_cloud()
    {
        $admin = factory(User::class)->state('admin')->create();
        $cloud = factory(Cloud::class)->create();

        $this->assertTrue(
            $this->policy->viewAny($admin, $cloud),
            "Admin cannot view a specific cloud."
        );
    }

    public function test_a_team_member_can_view_a_cloud_that_belongs_to_their_current_team()
    {
        $team =  factory(Team::class)->create();
        $user = factory(User::class)->create();
        resolve(AddTeamMember::class)->execute(
            $team,
            $user
        );

        $cloud = factory(Cloud::class)->create(['team_id' => $team->id]);

        $this->assertTrue(
            $this->policy->view($user, $cloud),
            "Team member cannot view a cloud their team manages."
        );
    }

    public function test_cannot_view_a_cloud_belonging_to_another_team()
    {
        $cloud = factory(Cloud::class)->create();
        $user = factory(User::class)->create();
        // factory(Team::class)->create(['owner_id' => $user->id]);

        $this->assertNull(
            $this->policy->view($user, $cloud),
            "A user outside the cloud team is able to view it."
        );
    }

    public function test_it_permits_a_team_member_with_the_permission_to_create()
    {
        $team = factory(Team::class)->create();
        $user = factory(User::class)->create(['team_id' => $team->id]);
        resolve(AddTeamMember::class)->execute(
            $team,
            $user,
            [
                TeamPermission::CreateCloud
            ]
        );

        $this->assertTrue(
            $this->policy->create($user, new Cloud),
            "Team member with permission to create a cloud on current team was denied."
        );
    }
}
