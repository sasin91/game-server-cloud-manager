<?php

namespace Tests\Feature;

use App\Events\TeamInvitationAccepted;
use App\Events\TeamInvitationCreated;
use App\Events\TeamInvitationDeclined;
use App\Events\TeamInvitationSent;
use App\Team;
use App\User;
use Tests\TestCase;
use App\TeamInvitation;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\TeamInvitation as TeamInvitationNotification;
use Illuminate\Database\Eloquent\Model;

class TeamInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_invite_to_team()
    {
        $this->expectsEvents([TeamInvitationSent::class, TeamInvitationCreated::class]);
        Model::setEventDispatcher($this->app['events']);

        Notification::fake();

        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['owner_id' => $user->id]);

        $recipient = factory(User::class)->create();

        $this->be($user);

        $this->json('POST', route('teams.invitations.store', $team), [
            'recipient_id' => $recipient->id
        ])
        ->assertSuccessful();

        $this->assertDatabaseHas('team_invitations', [
            'team_id' => $team->id,
            'creator_id' => $user->id,
            'recipient_id' => $recipient->id
        ]);

        Notification::assertSentTo($recipient, TeamInvitationNotification::class);
    }

    public function test_recipient_can_join_a_team()
    {
        $this->expectsEvents(TeamInvitationAccepted::class);
        Model::setEventDispatcher($this->app['events']);

        $team = factory(Team::class)->create();
        $recipient = factory(User::class)->create();

        $invitation = factory(TeamInvitation::class)->create([
            'team_id' => $team->id,
            'recipient_id' => $recipient->id
        ]);

        $this->actingAs($recipient)
            ->postJson(route('team-invitations.accept'), ['token' => $invitation->token])
            ->assertSuccessful();

        $this->assertDatabaseHas('team_members', [
            'team_id' => $team->id,
            'user_id' => $recipient->id
        ]);

        tap($invitation->fresh(), function (TeamInvitation $invitation) {
            $this->assertTrue($invitation->trashed());
            $this->assertNotNull($invitation->accepted_at);
        });
    }

    public function test_recipient_can_decline_an_invitation()
    {
        $this->expectsEvents(TeamInvitationDeclined::class);
        Model::setEventDispatcher($this->app['events']);

        $team = factory(Team::class)->create();
        $recipient = factory(User::class)->create();

        $invitation = factory(TeamInvitation::class)->create([
            'team_id' => $team->id,
            'recipient_id' => $recipient->id
        ]);

        $this->actingAs($recipient)
            ->postJson(route('team-invitations.decline'), ['token' => $invitation->token])
            ->assertSuccessful();

        $this->assertDatabaseMissing('team_members', [
            'team_id' => $team->id,
            'user_id' => $recipient->id
        ]);

        tap($invitation->fresh(), function (TeamInvitation $invitation) {
            $this->assertTrue($invitation->trashed());
            $this->assertNull($invitation->accepted_at);
        });
    }

    public function test_cannot_accept_an_invitation_to_another_user()
    {
        $this->doesntExpectEvents(TeamInvitationAccepted::class);
        Model::setEventDispatcher($this->app['events']);

        $invitation = factory(TeamInvitation::class)->create();

        $this->be(factory(User::class)->create());

        $this->postJson(route('team-invitations.accept'), ['token' => $invitation->token])
            ->assertNotFound();
    }
}
