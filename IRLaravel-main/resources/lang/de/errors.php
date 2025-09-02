<?php

$originalTranslations = [
    'invalid_link' => [
        'title' => 'HOPPLA... DER LINK IST NICHT MEHR GÜLTIG.',
        'description' => 'Der von Ihnen verwendete Link ist nicht mehr gültig. Bitte versuchen Sie es erneut.',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
