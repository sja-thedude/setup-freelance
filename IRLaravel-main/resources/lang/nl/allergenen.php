<?php

$originalTranslations = [
    'types' => [
        'ei' => 'EI',
        'gluten' => 'GLUTEN',
        'lupine' => 'LUPINE',
        'melk' => 'MELK',
        'mosterd' => 'MOSTERD',
        'noten' => 'NOTEN',
        'pindas' => 'PINDAS',
        'schaald' => 'SCHAALD',
        'selderij' => 'SELDERIJ',
        'sesamzaad' => 'SESAMZAAD',
        'soja' => 'SOJA',
        'vis' => 'VIS',
        'weekdieren' => 'WEEKDIEREN',
        'zwavel' => 'ZWAVEL',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);