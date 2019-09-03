<?php

namespace Tests\Feature;

use App\Team;
use App\User;
use App\Cloud;
use App\Realm;
use App\Server;
use App\Deployment;
use Tests\TestCase;
use App\Actions\AddTeamMember;
use App\Events\TeamCreated;
use App\Events\TeamDeleted;
use App\Events\TeamRestored;
use App\Events\TeamUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_newly_created_team_has_the_current_user_as_owner_by_default()
    {
        $this->expectsEvents(TeamCreated::class);
        Model::setEventDispatcher($this->app['events']);

        $this->be(
            $user = factory(User::class)->create()
        );

        $team = $this->json('POST', route('teams.store'), [
            'name' => 'Team Galactic'
        ])->assertSuccessful();

        $this->assertDatabaseHas('teams', [
            'name' => 'Team Galactic',
            'owner_id' => $user->id
        ]);

        $this->assertDatabaseHas('team_members', [
            'user_id' => $user->id,
            'team_id' => $team->json('id')
        ]);
    }

    public function test_the_team_owner_can_update_the_team()
    {
        $this->expectsEvents(TeamUpdated::class);
        Model::setEventDispatcher($this->app['events']);

        $teamOwner = factory(User::class)->create();

        $team = factory(Team::class)->create(['owner_id' => $teamOwner->id]);

        $this->actingAs($teamOwner)
            ->patchJson(route('teams.update', $team), ['name' => 'Team Vengabois'])
            ->assertSuccessful();

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'Team Vengabois'
        ]);
    }

    public function test_a_team_member_cannot_update_the_team()
    {
        $this->doesntExpectEvents(TeamUpdated::class);
        Model::setEventDispatcher($this->app['events']);

        $team = factory(Team::class)->create();

        resolve(AddTeamMember::class)->execute(
            $team,
            $teamMember = factory(User::class)->create()
        );

        $this->actingAs($teamMember)
            ->patchJson(route('teams.update', $team), ['name' => 'Thing that should not be'])
            ->assertForbidden();
    }

    public function test_team_owner_can_delete_their_team()
    {
        $this->expectsEvents(TeamDeleted::class);
        Model::setEventDispatcher($this->app['events']);

        $teamOwner = factory(User::class)->create();

        $team = factory(Team::class)->create(['owner_id' => $teamOwner->id]);

        $this->actingAs($teamOwner)
            ->json('DELETE', route('teams.destroy', $team))
            ->assertSuccessful();

        $this->assertSoftDeleted('teams', ['id' => $team->id]);
    }

    public function test_team_member_cannot_delete_their_team()
    {
        $this->doesntExpectEvents(TeamDeleted::class);
        Model::setEventDispatcher($this->app['events']);

        $team = factory(Team::class)->create();
        $user = factory(User::class)->create();
        resolve(AddTeamMember::class)->execute(
            $team,
            $user
        );

        $this->actingAs($user)
            ->json('DELETE', route('teams.destroy', $team))
            ->assertForbidden();

        $this->assertDatabaseHas('teams', ['id' => $team->id]);
    }

    public function test_deleting_a_team_cascades_to_related_models()
    {
        $this->markTestIncomplete("Enable when deployments and realms exists.");

        $team = factory(Team::class)->create();

        $cloud = factory(Cloud::class)->create(['team_id' => $team->id]);
        $serverInCloud = factory(Server::class)->create(['cloud_id' => $cloud->id]);
        $serverDeployment = factory(Deployment::class)->create(['server_id' => $serverInCloud->id]);
        $realm = factory(Realm::class)->create(['server_id' => $serverInCloud->id]);

        $team->delete();

        $this->assertSoftDeleted('teams', ['id' => $team->id]);
        $this->assertSoftDeleted('servers', ['id' => $serverInCloud->id]);
        $this->assertSoftDeleted('deployments', ['id' => $serverDeployment->id]);
        $this->assertSoftDeleted('realms', ['id' => $realm->id]);
    }

    public function test_team_owner_can_restore_their_team()
    {
        $this->expectsEvents([TeamRestored::class]);
        Model::setEventDispatcher($this->app['events']);

        $teamOwner = factory(User::class)->create();

        $team = factory(Team::class)->state('trashed')->create(['owner_id' => $teamOwner->id]);

        $this->actingAs($teamOwner)
            ->json('POST', route('teams.restore', $team))
            ->assertSuccessful();

        $this->assertDatabaseHas('teams', ['id' => $team->id]);
    }

    /**
     * @todo
     */
    public function test_restoring_a_team_cascades_to_related_models()
    {
        $this->markTestIncomplete("Enable when deployments and realms exists.");

        $team = factory(Team::class)->state('trashed')->create();

        $cloud = factory(Cloud::class)->state('trashed')->create(['team_id' => $team->id]);
        $serverInCloud = factory(Server::class)->state('trashed')->create(['cloud_id' => $cloud->id]);
        $serverDeployment = factory(Deployment::class)->state('trashed')->create(['server_id' => $serverInCloud->id]);
        $realm = factory(Realm::class)->state('trashed')->create(['server_id' => $serverInCloud->id]);

        $team->restore();

        $this->assertDatabaseHas('teams', ['id' => $team->id]);
        $this->assertDatabaseHas('servers', ['id' => $serverInCloud->id]);
        $this->assertDatabaseHas('deployments', ['id' => $serverDeployment->id]);
        $this->assertDatabaseHas('realms', ['id' => $realm->id]);
    }
}
