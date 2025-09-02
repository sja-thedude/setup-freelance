<?php

use App\Jobs\SendAdminPasswordEmail;
use Faker\Generator as Faker;

$factory->define(App\Models\Email::class, function (Faker $faker) {
    return [
        'subject' => $faker->name,
        'to' => $faker->email,
        'locale' => config('app.locale'),
        'location' => json_encode(['id' => SendAdminPasswordEmail::class]),
        'content' => $faker->paragraphs(5, true),
    ];
});
