<?php

$originalTranslations = [
    'daysOfWeek' => [
        'sunday' => 'So',
        'monday' => 'Mo',
        'tuesday' => 'Di',
        'wednesday' => 'Mi',
        'thursday' => 'Do',
        'friday' => 'Fr',
        'saturday' => 'Sa',
    ],
    'monthNames' => [
        'january' => 'Jan',
        'february' => 'Feb',
        'march' => 'Mär',
        'april' => 'Apr',
        'may' => 'Mai',
        'june' => 'Jun',
        'july' => 'Jul',
        'august' => 'Aug',
        'september' => 'Sep',
        'october' => 'Okt',
        'november' => 'Nov',
        'december' => 'Dez',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
