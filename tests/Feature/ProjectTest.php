<?php

namespace Tests\Feature;

use App\Actions\AddTeamMember;
use App\Enums\TeamPermission;
use App\Environment;
use App\Project;
use App\Team;
use App\User;
use App\VersionControl;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_lists_the_projects_created_by_the_user_current_team()
    {
        $team = factory(Team::class)->create();

        $team->projects()->save(
            factory(Project::class)->make([
                'repository_url' => 'https://github.com/sasin91/game-server-cloud-manager'
            ])
        );

        $user = factory(User::class)->create(['team_id' => $team->id]);
        $user->currentTeam()->associate($team);

        $this->actingAs($user)
        ->json('GET', route('projects.index'))
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertSee('https:\/\/github.com\/sasin91\/game-server-cloud-manager');
    }

    public function test_can_view_project_within_current_team()
    {
        $team = factory(Team::class)->create();
        $project = factory(Project::class)->create([
            'team_id' => $team->id
        ]);

        $this->actingAs(
            factory(User::class)->create(['team_id' => $team->id])
        )
        ->json('GET', route('projects.show', $project))
        ->assertSuccessful()
        ->assertJsonFragment(['id' => $project->id]);
    }

    public function test_cannot_view_project_outside_current_team()
    {
        $project = factory(Project::class)->create();

        $this->actingAs(
            factory(User::class)->state('with team')->create()
        )
        ->json('GET', route('projects.show', $project))
        ->assertForbidden();
    }

    public function test_can_store_a_project()
    {
        $team = factory(Team::class)->create();
        $user = factory(User::class)->create(['team_id' => $team->id]);
        $user->currentTeam()->associate($team);

        $sharedEnvironment = factory(Environment::class)->create();

        $this->actingAs($user)
        ->json('POST', route('projects.store'), [
            'environment_id' => $sharedEnvironment->id,
            'repository_url' => 'https://github.com/sasin91/game-server-cloud-manager',
            'version_control' => 'GitHub'
        ])
        ->assertSuccessful();

        $this->assertDatabaseHas('projects', [
            'team_id' => $team->id,
            'environment_id' => $sharedEnvironment->id,
            'repository_url' => 'https://github.com/sasin91/game-server-cloud-manager',
            'version_control' => 'GitHub'
        ]);
    }

    public function test_cannot_store_a_project_with_a_current_team()
    {
        $this
        ->actingAs(factory(User::class)->create())
        ->json('POST', route('projects.store'))
        ->assertForbidden();
    }

    public function test_validation_fails_if_version_control_is_invalid()
    {
        $team = factory(Team::class)->create();
        $user = factory(User::class)->create(['team_id' => $team->id]);
        $user->currentTeam()->associate($team);

        $this->actingAs($user)
        ->json('POST', route('projects.store'), [
            'environment' => ['key' => 'value'],
            'repository_url' => 'https://github.com/sasin91/game-server-cloud-manager',
            'version_control' => 'Invalid'
        ])
        ->assertJsonValidationErrors('version_control');
    }

    public function test_team_owner_can_update_a_project()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['owner_id' => $user->id]);
        $user->currentTeam()->associate($team)->saveOrFail();

        $project = factory(Project::class)->create(['team_id' => $team->id]);
        $environment = factory(Environment::class)->create();

        $this
        ->actingAs($user)
        ->json('PUT', route('projects.update', $project), [
            'environment_id' => $environment->id,
            'repository_url' => 'https://github.com/sasin91/game-server-cloud-manager',
            'version_control' => $vcs = VersionControl::$registered->random()->name
        ])
        ->assertSuccessful();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'environment_id' => $environment->id,
            'repository_url' => 'https://github.com/sasin91/game-server-cloud-manager',
            'version_control' => $vcs
        ]);
    }

    public function test_team_member_with_perm_can_update_a_project()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();
        \resolve(AddTeamMember::class)->execute(
            $team,
            $user,
            [
                TeamPermission::UpdateProject
            ]
        );
        $user->update(['team_id' => $team->id]);

        $project = factory(Project::class)->create(['team_id' => $team->id]);
        $environment = factory(Environment::class)->create();

        $this
        ->actingAs($user)
        ->json('PUT', route('projects.update', $project), [
            'environment_id' => $environment->id,
            'repository_url' => 'https://github.com/sasin91/game-server-cloud-manager',
            'version_control' => $vcs = VersionControl::$registered->random()->name
        ])
        ->assertSuccessful();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'environment_id' => $environment->id,
            'repository_url' => 'https://github.com/sasin91/game-server-cloud-manager',
            'version_control' => $vcs
        ]);
    }

    public function test_team_owner_can_destroy_a_project()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['owner_id' => $user->id]);
        $user->currentTeam()->associate($team)->saveOrFail();

        $project = factory(Project::class)->create(['team_id' => $team->id]);

        $this->actingAs($user)
            ->json('DELETE', route('projects.destroy', $project))
            ->assertSuccessful();

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_team_owner_can_restore_a_deleted_project()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['owner_id' => $user->id]);
        $user->currentTeam()->associate($team)->saveOrFail();

        $project = factory(Project::class)->state('trashed')->create(['team_id' => $team->id]);

        $this->actingAs($user)
            ->json('POST', route('projects.restore', $project))
            ->assertSuccessful();

        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    public function test_any_team_member_cannot_destroy_a_project()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();
        \resolve(AddTeamMember::class)->execute(
            $team,
            $user
        );

        $project = factory(Project::class)->create(['team_id' => $team->id]);

        $this
        ->actingAs($user)
        ->json('DELETE', route('projects.destroy', $project))
        ->assertForbidden();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id
        ]);
    }

    public function test_any_team_member_cannot_restore_a_project()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();
        \resolve(AddTeamMember::class)->execute(
            $team,
            $user
        );

        $project = factory(Project::class)->state('trashed')->create(['team_id' => $team->id]);

        $this
        ->actingAs($user)
        ->json('POST', route('projects.restore', $project))
        ->assertForbidden();

        $this->assertSoftDeleted('projects', [
            'id' => $project->id
        ]);
    }
}
