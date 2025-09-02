<?php

$originalTranslations = [
    'invalid_link' => [
        'title' => 'OUPS... LE LIEN N\'EST PLUS VALIDE.',
        'description' => 'Le lien que vous avez utilisé n\'est plus valide. Veuillez réessayer.',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
