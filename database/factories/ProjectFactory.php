<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Environment;
use App\Team;
use App\Project;
use App\VersionControl;
use Faker\Generator as Faker;

$factory->define(Project::class, function (Faker $faker) {
    return [
        'team_id' => factory(Team::class),
        'environment_id' => factory(Environment::class),
        'repository_url' => $faker->url,
        'version_control' => VersionControl::$registered->random()->name
    ];
});

$factory->state(Project::class, 'trashed', [(new Project)->getDeletedAtColumn() => now()]);
