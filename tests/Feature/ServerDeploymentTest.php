<?php

namespace Tests\Feature;

use App\Actions\AddTeamMember;
use App\Cloud;
use App\Deployment;
use App\Enums\DeploymentStatus;
use App\Events\DeploymentCreated;
use App\Jobs\DeployProjectOnServer;
use App\Project;
use App\Server;
use App\Team;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServerDeploymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_owner_can_list_the_server_deployments()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['owner_id' => $user->id]);
        $user->update(['team_id' => $team->id]);

        $cloud = factory(Cloud::class)->create(['team_id' => $team->id]);
        $server = factory(Server::class)->create(['cloud_id' => $cloud->id]);

        $project = factory(Project::class)->create(['team_id' => $team->id]);

        $server->deployments()->save(
            new Deployment([
                'project_id' => $project->id,
                'script' => 'testing',
                'status' => DeploymentStatus::EXECUTING,
                'exitcode' => null,
                'output' => null
            ])
        );

        $this->actingAs($user)
            ->json('GET', route('server.deployments.index', $server))
            ->assertSuccessful()
            ->assertJsonFragment([
                'script' => 'testing',
                'status' => DeploymentStatus::EXECUTING,
            ]);
    }

    public function test_team_member_can_list_the_server_deployments()
    {
        $team = factory(Team::class)->create();
        $user = factory(User::class)->create();
        resolve(AddTeamMember::class)->execute(
            $team,
            $user
        );
        $user->update(['team_id' => $team->id]);

        $cloud = factory(Cloud::class)->create(['team_id' => $team->id]);
        $server = factory(Server::class)->create(['cloud_id' => $cloud->id]);

        $project = factory(Project::class)->create(['team_id' => $team->id]);

        $server->deployments()->save(
            new Deployment([
                'project_id' => $project->id,
                'script' => 'testing',
                'status' => DeploymentStatus::EXECUTING,
                'exitcode' => null,
                'output' => null
            ])
        );

        $this->actingAs($user)
            ->json('GET', route('server.deployments.index', $server))
            ->assertSuccessful()
            ->assertJsonFragment([
                'script' => 'testing',
                'status' => DeploymentStatus::EXECUTING,
            ]);
    }

    public function test_cannot_view_deployments_of_a_server_that_does_not_belong_to_the_current_team()
    {
        $server = factory(Server::class)->create();

        $this->actingAs(
            factory(User::class)->state('with team')->create()
        )->json('GET', route('server.deployments.index', $server))
        ->assertNotFound();
    }

    public function test_team_owner_can_deploy_a_project_to_a_server()
    {
        $this->expectsEvents(DeploymentCreated::class);
        Model::setEventDispatcher($this->app['events']);
        $this->expectsJobs(DeployProjectOnServer::class);

        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['owner_id' => $user->id]);
        $user->update(['team_id' => $team->id]);

        $cloud = factory(Cloud::class)->create(['team_id' => $team->id]);
        $server = factory(Server::class)->create(['cloud_id' => $cloud->id]);

        $project = factory(Project::class)->create(['team_id' => $team->id]);

        $this->actingAs($user)
            ->json('POST', route('server.deployments.store', $server), [
                'project_id' => $project->id,
                'script' => 'DeployAzerothCoreUsingDocker'
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas('deployments', [
            'server_id' => $server->id,
            'project_id' => $project->id,
            'script' => 'DeployAzerothCoreUsingDocker'
        ]);
    }
}
