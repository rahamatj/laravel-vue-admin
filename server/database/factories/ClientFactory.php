<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Client;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Client::class, function (Faker $faker) {
    return [
        'user_id' => $faker->numberBetween(1, 100),
        'fingerprint' => Str::random(10),
        'client' => $faker->userAgent,
        'platform' => 'Unknown',
        'ip' => $faker->ipv4,
        'logged_in_at' => now()
    ];
});
