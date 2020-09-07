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

$factory->define(App\Models\Account::class, function (Faker\Generator $faker) {
    return [
        'uuid'      => $faker->uuid,
        'username'  => $faker->name,
        'skin_md5'  => $faker->md5,
        'fail_count' => $faker->randomNumber(4),
        'updated'   => $faker->unixTime,
        'skin'      => $faker->sha256,
        'cape'      => $faker->sha256,
    ];
});


$factory->define(App\Models\AccountNameChange::class, function (Faker\Generator $faker) {
    return [
        'uuid' => $faker->uuid,
        'prev_name' => $faker->name,
        'new_name' => $faker->name,
        'time_change' => $faker->unixTime,
    ];
});

$factory->define(App\Models\AccountStats::class, function (Faker\Generator $faker) {
    return [
        'uuid' => $faker->uuid,
        'count_request' => $faker->randomNumber(6),
        'request_at' => $faker->dateTime,
    ];
});