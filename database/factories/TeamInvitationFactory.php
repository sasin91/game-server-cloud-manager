<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TeamInvitation;
use Faker\Generator as Faker;
use App\Team;
use App\TeamInvitationToken;
use App\User;

$factory->define(TeamInvitation::class, function (Faker $faker) {
    return [
        'team_id' => factory(Team::class),
        'creator_id' => factory(User::class),
        'recipient_id' => factory(User::class),
        'token' => TeamInvitationToken::make(),
        'sent_at' => null
    ];
});
