<?php

namespace Tests\Feature;

use App\Actions\AddTeamMember;
use App\Team;
use App\User;
use App\Cloud;
use App\CloudProvider;
use Tests\TestCase;
use App\Environment;
use App\Exceptions\CloudProviderNotFound;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Contracts\CloudProvider as CloudProviderContract;
use App\Enums\TeamPermission;
use App\Events\CloudCreated;
use App\Events\CloudDeleted;
use App\Events\CloudUpdated;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Eloquent\Model;

class CloudTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_configures_the_cloud_provider_instance()
    {
        $cloud = factory(Cloud::class)->create([
            'provider' => 'DigitalOcean',
            'environment_id' => factory(Environment::class)->create(['variables' => ['provider_token' => 'fake-do-co-token']])
        ]);

        $provider = $cloud->provider();
        $this->assertInstanceOf(CloudProviderContract::class, $provider);
        $this->assertEquals('Bearer fake-do-co-token', $provider->client()->getConfig('headers')['Authorization']);
    }

    public function test_it_throws_an_exception_if_the_cloud_provider_does_not_exist()
    {
        $cloud = factory(Cloud::class)->create([
            'provider' => 'Invalid-cloud-provider'
        ]);

        try {
            $cloud->provider();

            $this->fail('Expected CloudProviderNotFound to be thrown.');
        } catch (CloudProviderNotFound $e) {
            $this->assertEquals(
                "InvalidCloudProvider is not supported.",
                $e->getMessage()
            );
        }
    }

    public function test_team_member_only_see_their_current_teams_clouds()
    {
        $team = factory(Team::class)->create();
        $user = factory(User::class)->create(['team_id' => $team->id]);

        $otherTeam = factory(Team::class)->create();
        resolve(AddTeamMember::class)->execute(
            $otherTeam,
            $user
        );

        $cloud = factory(Cloud::class)->create([
            'team_id' => $team->id,
            'provider' => 'DigitalOcean'
        ]);

        $anotherCloud = factory(Cloud::class)->create([
            'team_id' => $otherTeam->id,
            'provider' => 'AWS'
        ]);

        $otherTeamCloud = factory(Cloud::class)->create(['provider' => 'CustomRestAPI']);

        $this->actingAs($user)
            ->json('GET', route('clouds.index'))
            ->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->assertSee($cloud->provider)
            ->assertDontSee($anotherCloud->provider)
            ->assertDontSee($otherTeamCloud->provider);
    }

    public function test_team_owner_can_update_a_cloud()
    {
        $this->expectsEvents(CloudUpdated::class);
        Model::setEventDispatcher($this->app['events']);

        $teamOwner = factory(User::class)->create();
        $team = factory(Team::class)->create(['owner_id' => $teamOwner->id]);
        $cloud = factory(Cloud::class)->create(['team_id' => $team->id]);

        $this->actingAs($teamOwner)
            ->json('PUT', route('clouds.update', $cloud), [
                'provider' => 'CustomRestAPI',
                'private_network' => '172.16.0.0/24',
                'address' => 'login.my-game-servers.tld'
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas('clouds', [
            'id' => $cloud->id,
            'provider' => 'CustomRestAPI',
            'private_network' => '172.16.0.0/24',
            'address' => 'login.my-game-servers.tld'
        ]);
    }

    public function test_team_owner_can_delete_their_team()
    {
        $this->expectsEvents(CloudDeleted::class);
        Model::setEventDispatcher($this->app['events']);

        $teamOwner = factory(User::class)->create();
        $team = factory(Team::class)->create(['owner_id' => $teamOwner->id]);
        $cloud = factory(Cloud::class)->create(['team_id' => $team->id]);

        $this->actingAs($teamOwner)
            ->json('DELETE', route('clouds.destroy', $cloud))
            ->assertSuccessful();

        $this->assertSoftDeleted('clouds', ['id' => $cloud->id]);
    }

    public function test_team_owner_can_create_a_new_cloud()
    {
        $this->expectsEvents(CloudCreated::class);
        Model::setEventDispatcher($this->app['events']);

        $teamOwner = factory(User::class)->create();
        $team = factory(Team::class)->create(['owner_id' => $teamOwner->id]);
        $teamOwner->currentTeam()->associate($team)->saveOrFail();

        $this->actingAs($teamOwner)
            ->json('POST', route('clouds.store'), [
                'environment' => [
                    'provider_token' => 'my-api-token'
                ],
                'provider' => 'CustomRestAPI',
                'private_network' => '172.16.1.0/24',
                'address' => 'login.my-game-servers.tld'
            ])
            ->assertSuccessful()
            ->assertJsonFragment([
                'provider_token' => 'my-api-token'
            ]);

        $this->assertDatabaseHas('clouds', [
            'team_id' => $team->id,
            'provider' => 'CustomRestAPI',
            'private_network' => '172.16.1.0/24',
            'address' => 'login.my-game-servers.tld'
        ]);
    }

    public function test_team_member_with_permission_to_create_clouds_can_create_a_cloud()
    {
        $this->expectsEvents(CloudCreated::class);
        Model::setEventDispatcher($this->app['events']);

        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();
        resolve(AddTeamMember::class)->execute(
            $team,
            $user,
            [
                TeamPermission::CreateCloud
            ]
        );
        $user->currentTeam()->associate($team)->saveOrFail();

        $this->actingAs($user)
            ->json('POST', route('clouds.store'), [
                'environment' => [
                    'provider_token' => 'my-api-token'
                ],
                'provider' => 'DigitalOcean',
                'private_network' => '192.168.10.0/28',
                'address' => 'my-wow-servers.com'
            ])
            ->assertSuccessful()
            ->assertJsonFragment([
                'provider_token' => 'my-api-token'
            ]);

        $this->assertDatabaseHas('clouds', [
            'team_id' => $team->id,
            'provider' => 'DigitalOcean',
            'private_network' => '192.168.10.0/28',
            'address' => 'my-wow-servers.com'
        ]);
    }

    public function test_team_member_without_permission_to_create_clouds_cannot_create_a_cloud()
    {
        $this->doesntExpectEvents(CloudCreated::class);
        Model::setEventDispatcher($this->app['events']);

        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();
        resolve(AddTeamMember::class)->execute(
            $team,
            $user
        );
        $user->currentTeam()->associate($team)->saveOrFail();

        $this->actingAs($user)
            ->json('POST', route('clouds.store'), [
                'environment' => [
                    'provider_token' => 'my-api-token'
                ],
                'provider' => 'DigitalOcean',
                'private_network' => '192.168.11.0/24',
                'address' => 'not.my-wow-servers.com'
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('clouds', [
            'team_id' => $team->id,
            'provider' => 'DigitalOcean',
            'private_network' => '192.168.11.0/24',
            'address' => 'not.my-wow-servers.com'
        ]);
    }

    public function test_user_without_a_current_team_cannot_create_a_cloud()
    {
        $this->doesntExpectEvents(CloudCreated::class);
        Model::setEventDispatcher($this->app['events']);

        $this->actingAs(factory(User::class)->create())
            ->json('POST', route('clouds.store'), [])
            ->assertForbidden();
    }
}
