<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Category::class, function (Faker $faker) {
    return [
        'workspace_id' => \App\Models\Workspace::inRandomOrder()->first()->getKey(),
        'name' => $faker->name,
    ];
});
