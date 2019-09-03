<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Cloud;
use App\Enums\ServerStatus;
use App\Environment;
use App\Server;
use Faker\Generator as Faker;

$factory->define(Server::class, function (Faker $faker) {
    return [
        'environment_id' => factory(Environment::class),
        'cloud_id' => factory(Cloud::class),
        'status' => $faker->randomElement(ServerStatus::getValues()),
        'image' => 'ubuntu-18-04-x64',
        'private_address' => $faker->localIpv4,
        'public_address' => $faker->ipv4,
        'provider_id' => $faker->uuid
    ];
});
