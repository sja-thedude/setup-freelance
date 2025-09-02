<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Coupon::class, function (Faker $faker) {
    return [
        'active' => true,
        'workspace_id' => \App\Models\Workspace::inRandomOrder()->first()->getKey(),
        'code' => strtoupper($faker->regexify('[A-Za-z0-9]{10}')),
        'promo_name' => $faker->text(),
        'max_time_all' => $faker->randomNumber(2),
        'max_time_single' => $faker->randomNumber(2),
        'currency' => $faker->currencyCode,
        'discount' => $faker->randomFloat(2, 0, 100),
        'expire_time' => $faker->dateTimeBetween('now', '+10 year'),
    ];
});
