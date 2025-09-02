<?php

$originalTranslations = [
    'token_invalid' => 'Token ongeldig',
    'provider_invalid' => 'Provider ongeldig',
    'login_success' => 'Succes',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);