<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var Factory $factory */

use App\LinkClick;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'email' => $faker->unique()->safeEmail,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'avatar' => $faker->imageUrl(64, 64),
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(LinkClick::class, function (Faker\Generator $faker) {
    return [
        'link_type' => array_random(['frame', 'direct', 'overlay', 'splash']),
        'location' => $faker->countryCode,
        'ip' => $faker->ipv4,
        'platform' => array_random(['windows', 'linux', 'ios', 'androidos']),
        'device' => array_random(['mobile', 'tablet', 'desktop']),
        'crawler' => false,
        'browser' => array_random(['chrome', 'firefox', 'edge', 'internet exporer', 'safari']),
        'referrer' => $faker->url,
        'created_at' => $faker->dateTimeBetween(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()),
    ];
});
