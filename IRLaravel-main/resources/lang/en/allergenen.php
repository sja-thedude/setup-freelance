<?php

$originalTranslations = [
    'types' => [
        'ei' => 'EGG',
        'gluten' => 'GLUTEN',
        'lupine' => 'LUPIN',
        'melk' => 'MILK',
        'mosterd' => 'MUSTARD',
        'noten' => 'NUTS',
        'pindas' => 'PEANUTS',
        'schaald' => 'CRUSTACEANS',
        'selderij' => 'CELERY',
        'sesamzaad' => 'SESAME SEEDS',
        'soja' => 'SOY',
        'vis' => 'FISH',
        'weekdieren' => 'MOLLUSCS',
        'zwavel' => 'SULPHUR',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);