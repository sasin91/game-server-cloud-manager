<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Deployment;
use App\Enums\DeploymentStatus;
use App\Project;
use App\Server;
use Faker\Generator as Faker;

$factory->define(Deployment::class, function (Faker $faker) {
    return [
        'server_id' => factory(Server::class),
        'project_id' => factory(Project::class),
        'script' => '',
        'status' => $faker->randomElement(DeploymentStatus::getValues()),
        'exitcode' => null,
        'output' => null
    ];
});
