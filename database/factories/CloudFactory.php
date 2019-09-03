<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Cloud;
use App\CloudProvider;
use Faker\Generator as Faker;
use App\Environment;
use App\Team;

$factory->define(Cloud::class, function (Faker $faker) {
    return [
        'team_id' => factory(Team::class),
        'environment_id' => factory(Environment::class),
        'provider' => CloudProvider::$registered->random()->name,
        'private_network' => '127.0.0.1',
        'address' => $faker->url
    ];
});
