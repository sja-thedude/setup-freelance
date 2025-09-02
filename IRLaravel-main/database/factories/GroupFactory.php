<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Group::class, function (Faker $faker) {
    return [
        'workspace_id' => \App\Models\Workspace::inRandomOrder()->first()->getKey(),
        'name' => $faker->name,
        'company_name' => $faker->company,
        'company_street' => $faker->streetAddress,
        'company_number' => $faker->buildingNumber,
        'company_vat_number' => strtoupper($faker->regexify('[A-Za-z0-9]{10}')),
        'company_city' => $faker->city,
        'company_postcode' => $faker->postcode,
        'payment_mollie' => $faker->randomKey(array(0, 1)),
        'payment_payconiq' => $faker->randomKey(array(0, 1)),
        'payment_cash' => $faker->randomKey(array(0, 1)),
        'payment_factuur' => $faker->randomKey(array(0, 1)),
        'close_time' => $faker->time(),
        'receive_time' => $faker->time(),
        'type' => $faker->randomKey(array(1, 2)),
        'contact_email' => $faker->email,
        'contact_name' => $faker->firstName,
        'contact_surname' => $faker->lastName,
        'contact_gsm' => $faker->phoneNumber
    ];
});
