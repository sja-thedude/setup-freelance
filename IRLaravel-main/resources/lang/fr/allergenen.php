<?php

$originalTranslations = [
    'types' => [
        'ei' => 'OEUF',
        'gluten' => 'GLUTEN',
        'lupine' => 'LUPIN',
        'melk' => 'LAIT',
        'mosterd' => 'MOUTARDE',
        'noten' => 'FRUITS À COQUE',
        'pindas' => 'ARACHIDES',
        'schaald' => 'CRUSTACÉS',
        'selderij' => 'CÉLERI',
        'sesamzaad' => 'SÉSAME',
        'soja' => 'SOJA',
        'vis' => 'POISSON',
        'weekdieren' => 'MOLLUSQUES',
        'zwavel' => 'SULFITES',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);