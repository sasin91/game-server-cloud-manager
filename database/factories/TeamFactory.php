<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Team;
use Faker\Generator as Faker;
use App\User;

$factory->define(Team::class, function (Faker $faker) {
    return [
        'name' => $faker->bs,
        'owner_id' => factory(User::class)
    ];
});

$factory->state(Team::class, 'trashed', ['deleted_at' => now()]);
