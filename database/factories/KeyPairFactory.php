<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\KeyPair;
use App\User;
use Faker\Generator as Faker;

$factory->define(KeyPair::class, function (Faker $faker) {
    return [
        'owner_type' => User::class,
        'owner_id' => factory(User::class),
        'encryption_key' => substr(config('app.key'), 7),
        'public_key' => '',
        'private_key' => ''
    ];
});
