<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Product::class, function (Faker $faker) {
    return [
        'workspace_id' => \App\Models\Workspace::inRandomOrder()->first()->getKey(),
        'category_id' => \App\Models\Category::inRandomOrder()->first()->getKey(),
        'name' => $faker->name,
        'currency' => $faker->currencyCode,
        'price' => $faker->randomNumber(4),
    ];
});
