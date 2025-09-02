<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Workspace::class, function (Faker $faker) {
    return [
        'user_id' => \App\Models\User::inRandomOrder()->first()->getKey(),
        'name' => $faker->firstName,
        'surname' => $faker->lastName,
        'active' => true,
        'account_manager_id' => \App\Models\User::inRandomOrder()->first()->getKey(),
        'gsm' => $faker->phoneNumber,
        'manager_name' => $faker->name,
        'address' => $faker->address,
        'address_lat' => $faker->latitude,
        'address_long' => $faker->longitude,
        'email' => $faker->email,
        'country_id' => \App\Models\Country::inRandomOrder()->first()->getKey(),
    ];
});
