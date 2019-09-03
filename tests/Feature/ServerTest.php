<?php

namespace Tests\Feature;

use App\Team;
use App\User;
use App\Cloud;
use App\Server;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Enums\ServerStatus;
use App\Actions\DeleteServer;
use App\Enums\TeamPermission;
use App\Actions\AddTeamMember;
use App\Events\ServerCreated;
use App\Events\ServerDeleted;
use App\Events\ServerUpdated;
use App\Jobs\DeleteServerInCloud;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function array_key_exists;
use function array_merge;
use function factory;
use function resolve;
use function route;

class ServerTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_owner_can_delete_any_server_within_team_clouds()
    {
        $this->expectsEvents(ServerDeleted::class);
        Model::setEventDispatcher($this->app['events']);

        $this->expectsJobs(DeleteServerInCloud::class);

        /** @var User $user */
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['owner_id' => $user->id]);
        $cloud = factory(Cloud::class)->create(['team_id' => $team->id]);
        $server = factory(Server::class)->create(['cloud_id' => $cloud->id]);

        $user->currentTeam()->associate($team);

        $this->actingAs($user)
            ->json('DELETE', route('servers.delete', $server))
            ->assertSuccessful();

        $this->assertSoftDeleted('servers', ['id' => $server->id]);
    }

    public function test_team_member_with_permission_granted_can_delete_a_server()
    {
        $this->expectsEvents(ServerDeleted::class);
        Model::setEventDispatcher($this->app['events']);

        $this->expectsJobs(DeleteServerInCloud::class);

        /** @var User $user */
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();
        $this->app->make(AddTeamMember::class)->execute(
            $team,
            $user,
            [
                TeamPermission::DeleteServerInCloud
            ]
        );
        $cloud = factory(Cloud::class)->create(['team_id' => $team->id]);
        $server = factory(Server::class)->create(['cloud_id' => $cloud->id]);

        $this->actingAs($user)
            ->json('DELETE', route('servers.delete', $server))
            ->assertSuccessful();

        $this->assertSoftDeleted('servers', ['id' => $server->id]);
    }

    public function test_any_team_member_without_permission_cannot_delete_a_server()
    {
        $this->doesntExpectEvents(ServerDeleted::class);
        Model::setEventDispatcher($this->app['events']);

        $this->doesntExpectJobs(DeleteServerInCloud::class);

        /** @var User $user */
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();
        $this->app->make(AddTeamMember::class)->execute(
            $team,
            $user
        );
        $cloud = factory(Cloud::class)->create(['team_id' => $team->id]);
        $server = factory(Server::class)->create(['cloud_id' => $cloud->id]);

        $this->actingAs($user)
            ->json('DELETE', route('servers.delete', $server))
            ->assertForbidden();
    }

    public function test_deleting_a_server_also_deletes_it_in_the_cloud()
    {
        $this->expectsEvents(ServerDeleted::class);
        Model::setEventDispatcher($this->app['events']);

        $this->expectsJobs(DeleteServerInCloud::class);

        $server = factory(Cloud::class)->create();
        $server = factory(Server::class)->create([
            'cloud_id' => $server->id,
            'image' => 'debian-10-x64',
            'provider_id' => 'my-digital-ocean-server-id'
        ]);

        $this->app->make(DeleteServer::class)->execute($server);

        $this->assertSoftDeleted('servers', ['id' => $server->id]);
    }

    public function test_the_index_lists_the_servers_in_the_cloud_of_the_current_team()
    {
        $team = factory(Team::class)->create();
        $cloudInCurrentTeam = factory(Cloud::class)->create([
            'team_id' => $team->id,
        ]);
        $serverInCurrentTeam = factory(Server::class)->create([
            'cloud_id' => $cloudInCurrentTeam->id,
            'image' => 'debian-10-x64'
        ]);
        $otherServer = factory(Server::class)->create(['image' => 'centos-7-x64']);

        $this->actingAs(
            factory(User::class)->create(['team_id' => $team->id])
        )
        ->json('GET', route('servers.index'))
        ->assertSuccessful()
        ->assertSee($serverInCurrentTeam->image)
        ->assertDontSee($otherServer->image);
    }

    public function test_authorization_fails_if_user_is_missing_a_current_team()
    {
        $this->actingAs(
            factory(User::class)->create()
        )
        ->json('GET', route('servers.index'))
        ->assertForbidden();
    }

    public function test_cannot_create_a_server_in_another_cloud()
    {
        $this->doesntExpectEvents(ServerCreated::class);
        Model::setEventDispatcher($this->app['events']);

        $this->be(
            factory(User::class)->state('with team')->create()
        )
            ->json('POST', route('servers.store'), $params = $this->validParamsForCreate())
            ->assertForbidden();

        unset($params['environment']);
        $this->assertDatabaseMissing('servers', $params);
    }

    private function validParamsForCreate(array $overrides = [])
    {
        $params = array_merge([
          'environment' => [
                'key' => 'value'
            ],
            'status' => ServerStatus::ONLINE,
            'image' => 'ubuntu-18-04-x64',
            'private_address' => '127.0.0.1',
            'public_address' => '192.168.0.2',
            'provider_id' => 'server-provider-id'
        ], $overrides);

        if (array_key_exists('cloud_id', $params) === false) {
            $params['cloud_id'] = factory(Cloud::class)->create()->id;
        }

        return $params;
    }

    private function validParamsForUpdate(array $overrides = [])
    {
        return array_merge([
            'status' => ServerStatus::DEPLOYING,
            'image' => 'ubuntu-18-04-x64',
            'private_address' => '127.0.0.1',
            'public_address' => '192.168.0.2',
            'provider_id' => 'server-provider-id'
        ], $overrides);
    }

    public function test_team_owner_can_create_a_server_in_the_team_cloud()
    {
        $this->expectsEvents(ServerCreated::class);
        Model::setEventDispatcher($this->app['events']);

        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['owner_id' => $user->id]);
        $cloud = factory(Cloud::class)->create(['team_id' => $team->id]);
        $user->update(['team_id' => $team->id]);

        $this->be($user)
            ->json('POST', route('servers.store'), $params = $this->validParamsForCreate(['cloud_id' => $cloud->id]))
            ->assertSuccessful();

        unset($params['environment']);
        $this->assertDatabaseHas('servers', $params);
    }

    public function test_team_member_with_permission_to_create_can_create_a_server_in_the_team_cloud()
    {
        $this->expectsEvents(ServerCreated::class);
        Model::setEventDispatcher($this->app['events']);

        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();
        $this->app->make(AddTeamMember::class)->execute(
            $team,
            $user,
            [
                TeamPermission::CreateServerInCloud
            ]
        );
        $cloud = factory(Cloud::class)->create(['team_id' => $team->id]);
        $user->update(['team_id' => $team->id]);

        $this->be($user)
            ->json('POST', route('servers.store'), $params = $this->validParamsForCreate(['cloud_id' => $cloud->id]))
            ->assertSuccessful();

        unset($params['environment']);
        $this->assertDatabaseHas('servers', $params);
    }

    public function test_team_member_without_the_permission_cannot_create_a_server()
    {
        $this->doesntExpectEvents(ServerCreated::class);
        Model::setEventDispatcher($this->app['events']);

        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();
        $this->app->make(AddTeamMember::class)->execute(
            $team,
            $user
        );
        $cloud = factory(Cloud::class)->create(['team_id' => $team->id]);

        $this->be($user)
            ->json('POST', route('servers.store'), $params = $this->validParamsForCreate(['cloud_id' => $cloud->id]))
            ->assertForbidden();

        // Environment doesn't exist in servers table,
        unset($params['environment']);
        $this->assertDatabaseMissing('servers', $params);
    }

    public function test_team_owner_can_update_a_server_in_their_team()
    {
        $this->expectsEvents(ServerUpdated::class);
        Model::setEventDispatcher($this->app['events']);

        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['owner_id' => $user->id]);
        $cloud = factory(Cloud::class)->create(['team_id' => $team->id]);
        $user->update(['team_id' => $team->id]);

        $server = factory(Server::class)->create([
            'cloud_id' => $cloud->id
        ]);

        $this->be($user)
            ->json('PUT', route('servers.update', $server), $params = $this->validParamsForUpdate())
            ->assertSuccessful();

        $this->assertDatabaseHas('servers', $params + ['id' => $server->id]);
    }

    public function test_a_team_member_lacking_permission_cannot_update_a_server()
    {
        $this->doesntExpectEvents(ServerUpdated::class);
        Model::setEventDispatcher($this->app['events']);

        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();
        $this->app->make(AddTeamMember::class)->execute(
            $team,
            $user
        );
        $cloud = factory(Cloud::class)->create(['team_id' => $team->id]);
        $server = factory(Server::class)->create(['cloud_id' => $cloud->id]);

        $this->be($user)
            ->json('PUT', route('servers.update', $server), $this->validParamsForUpdate())
            ->assertForbidden();
    }
}
