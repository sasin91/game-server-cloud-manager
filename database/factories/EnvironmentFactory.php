<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Environment;
use Faker\Generator as Faker;

$factory->define(Environment::class, function (Faker $faker) {
    return [
        'encryption_key' => substr(config('app.key'), 7),
        'variables' => ''
    ];
});
