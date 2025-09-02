<?php

$originalTranslations = [
    'invalid_link' => [
        'title' => 'OOPS... THE LINK IS NO LONGER VALID.',
        'description' => 'The link you used is no longer valid. Please try again.',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
