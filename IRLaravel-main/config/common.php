<?php

return [
    'default_avatar' => 'images/default-user.png',
    'default_banner_image' => 'assets/images/banner-placeholder.jpg',
    'banner_directory' => 'uploads/banners/',
    'pagination' => 10,
    'setting_types' => [
        'takeout' => 0,
        'delivery' => 1,
        'in_house' => 2,
        'self_ordering' => 3,
    ],
    'setting_types_have_timeslots' => [
        'takeout' => 0,
        'delivery' => 1,
    ],
    'time_slots' => [
        '1' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        '10' => 10,
        '15' => 15,
        '20' => 20,
        '25' => 25,
        '30' => 30,
        '45' => 45,
        '60' => 60,
        '90' => 90,
        '120' => 120,
        '150' => 150,
        '180' => 180,
        '210' => 210,
        '240' => 240,
    ],
    'day_in_week' => [
        1,2,3,4,5,6,0
    ],
    'vat_level' => [
        '6' => 'C',
        '12' => 'B',
        '21' => 'A'
    ],
    'vat_level_trans' => [
        '6' => 'low',
        '12' => 'mid',
        '21' => 'hoog'
    ],
    'payment_convert' => [
        0 => 1,
        1 => 1,
        2 => 0,
        3 => 2
    ],
    'payment_method_convert' => [
        0 => [2],
        1 => [0, 1],
        2 => [3],
    ],
    'week_days' => [
        0 => 'sunday',
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday'
    ],
    'social_facebook' => env('SOCIAL_FACEBOOK', true),
    'social_google' => env('SOCIAL_GOOGLE', true),
    'social_apple' => env('SOCIAL_APPLE', true),
];
