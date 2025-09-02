<?php

use Faker\Generator as Faker;

$factory->define(App\Models\RestaurantCategory::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
