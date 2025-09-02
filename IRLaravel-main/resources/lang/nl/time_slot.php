<?php

$originalTranslations = [
    'title' => 'Tijdslots',
    'types' => [
        0 => 'Afhaal',
        1 => 'Levering',
        2 => 'Ter plaatse',
    ],
    'order_per_time_slot' => 'Aantal bestellingen/tijdslot',
    'max_price_per_time_slot' => 'Maximum bedrag/tijdslot',
    'interval_time_slot' => 'Interval tussen tijdslots',
    'order_maximum' => 'Bestellen tot maximaal',
    'updated_success' => 'Tijdvakken zijn succesvol bijgewerkt',
    'apply_with' => 'Toepasbaar op',
    'before_day_0' => 'Dezelfde dag',
    'before_day_1' => '1 dag eerder',
    'before_day_2' => '2 dagen eerder',
    'manage_dynamic_time_slot' => 'Tijdsloten dynamisch beheren',
    'mini_days' => [
        '1' => 'MA',
        '2' => 'DI',
        '3' => 'WO',
        '4' => 'DO',
        '5' => 'VR',
        '6' => 'ZA',
        '0' => 'ZO',
    ],
    'time' => 'Tijd',
    'time_mode' => 'Aan / uit',
    'max_best' => 'Max best./tijdsl',
    'each' => 'Elke',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);