<?php

$originalTranslations = [
    'title' => 'Time Slots',
    'types' => [
        0 => 'Takeout',
        1 => 'Delivery',
        2 => 'In-house',
    ],
    'order_per_time_slot' => 'Number of orders/time slot',
    'max_price_per_time_slot' => 'Maximum amount/time slot',
    'interval_time_slot' => 'Interval between time slots',
    'order_maximum' => 'Order up to maximum',
    'updated_success' => 'Time slots have been updated successfully',
    'apply_with' => 'Applicable to',
    'before_day_0' => 'Same day',
    'before_day_1' => '1 day earlier',
    'before_day_2' => '2 days earlier',
    'manage_dynamic_time_slot' => 'Manage dynamic time slots',
    'mini_days' => [
        '1' => 'MO',
        '2' => 'TU',
        '3' => 'WE',
        '4' => 'TH',
        '5' => 'FR',
        '6' => 'SA',
        '0' => 'SU',
    ],
    'time' => 'Time',
    'time_mode' => 'On / Off',
    'max_best' => 'Max orders/time slot',
    'each' => 'Each',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);