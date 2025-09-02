<?php

$originalTranslations = [
    'title' => 'Zeitfenster',
    'types' => [
        0 => 'Abholung',
        1 => 'Lieferung',
        2 => 'Im Haus',
    ],
    'order_per_time_slot' => 'Bestellungen pro Zeitfenster',
    'max_price_per_time_slot' => 'Maximaler Betrag pro Zeitfenster',
    'interval_time_slot' => 'Intervall zwischen Zeitfenstern',
    'order_maximum' => 'Bestellen bis maximal',
    'updated_success' => 'Zeitfenster wurden erfolgreich aktualisiert',
    'apply_with' => 'Anwendbar auf',
    'before_day_0' => 'Am selben Tag',
    'before_day_1' => '1 Tag vorher',
    'before_day_2' => '2 Tage vorher',
    'manage_dynamic_time_slot' => 'Dynamische Zeitfenster verwalten',
    'mini_days' => [
        '1' => 'MO',
        '2' => 'DI',
        '3' => 'MI',
        '4' => 'DO',
        '5' => 'FR',
        '6' => 'SA',
        '0' => 'SO',
    ],
    'time' => 'Zeit',
    'time_mode' => 'An / Aus',
    'max_best' => 'Max Best./Zeitfenster',
    'each' => 'Jede',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);