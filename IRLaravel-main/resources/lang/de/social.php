<?php

$originalTranslations = [
    'token_invalid' => 'Token ungültig',
    'provider_invalid' => 'Anbieter ungültig',
    'login_success' => 'Erfolg',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);