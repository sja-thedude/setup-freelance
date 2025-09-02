<?php

$originalTranslations = [
    'daysOfWeek' => [
        'sunday' => 'Zo',
        'monday' => 'Ma',
        'tuesday' => 'Di',
        'wednesday' => 'Wo',
        'thursday' => 'Do',
        'friday' => 'Vr',
        'saturday' => 'Za',
    ],
    'monthNames' => [
        'january' => 'Jan',
        'february' => 'Feb',
        'march' => 'Mrt',
        'april' => 'Apr',
        'may' => 'Mei',
        'june' => 'Jun',
        'july' => 'Jul',
        'august' => 'Aug',
        'september' => 'Sep',
        'october' => 'Okt',
        'november' => 'Nov',
        'december' => 'Dec',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
