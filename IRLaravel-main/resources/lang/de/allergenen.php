<?php

$originalTranslations = [
    'types' => [
        'ei' => 'EI',
        'gluten' => 'GLUTEN',
        'lupine' => 'LUPINE',
        'melk' => 'MILCH',
        'mosterd' => 'SENF',
        'noten' => 'NÜSSE',
        'pindas' => 'ERDNÜSSE',
        'schaald' => 'SCHALENTIERE',
        'selderij' => 'SELLERIE',
        'sesamzaad' => 'SESAMSAMEN',
        'soja' => 'SOJA',
        'vis' => 'FISCH',
        'weekdieren' => 'WEICHTIERE',
        'zwavel' => 'SCHWEFEL',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);