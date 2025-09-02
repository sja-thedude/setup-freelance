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

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->defineAs(App\Models\User::class, 'frontend', function (Faker\Generator $faker) {
    $firstName = $faker->firstName;
    $lastName = $faker->lastName;
    $name = $firstName . ' ' . $lastName;

    return [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'name' => $name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
        'role_id' => \App\Models\Role::where('platform', \App\Models\Role::PLATFORM_FRONTEND)->first()->getKey(),
        'active' => true,
        'is_verified' => true,
        'platform' => \App\Models\Role::PLATFORM_FRONTEND,
        'address' => $faker->address,
        'lat' => $faker->latitude,
        'lng' => $faker->longitude,
    ];
});
