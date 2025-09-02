<?php

$originalTranslations = [
    'invalid_link' => [
        'title' => 'OEPS... DE LINK IS NIET MEER GELDIG.',
        'description' => 'De link die u gebruikt heeft is niet meer geldig. Gelieve opnieuw te proberen.',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
