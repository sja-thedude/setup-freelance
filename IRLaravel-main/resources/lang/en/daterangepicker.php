<?php

$originalTranslations = [
    'daysOfWeek' => [
        'sunday' => 'Su',
        'monday' => 'Mo',
        'tuesday' => 'Tu',
        'wednesday' => 'We',
        'thursday' => 'Th',
        'friday' => 'Fr',
        'saturday' => 'Sa',
    ],
    'monthNames' => [
        'january' => 'Jan',
        'february' => 'Feb',
        'march' => 'Mar',
        'april' => 'Apr',
        'may' => 'May',
        'june' => 'Jun',
        'july' => 'Jul',
        'august' => 'Aug',
        'september' => 'Sep',
        'october' => 'Oct',
        'november' => 'Nov',
        'december' => 'Dec',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
