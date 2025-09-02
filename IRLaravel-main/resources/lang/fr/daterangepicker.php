<?php

$originalTranslations = [
    'daysOfWeek' => [
        'sunday' => 'Di',
        'monday' => 'Lu',
        'tuesday' => 'Ma',
        'wednesday' => 'Me',
        'thursday' => 'Je',
        'friday' => 'Ve',
        'saturday' => 'Sa',
    ],
    'monthNames' => [
        'january' => 'Jan',
        'february' => 'Fév',
        'march' => 'Mar',
        'april' => 'Avr',
        'may' => 'Mai',
        'june' => 'Juin',
        'july' => 'Juil',
        'august' => 'Août',
        'september' => 'Sep',
        'october' => 'Oct',
        'november' => 'Nov',
        'december' => 'Déc',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
