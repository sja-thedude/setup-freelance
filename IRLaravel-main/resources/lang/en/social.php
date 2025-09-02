<?php

$originalTranslations = [
    'token_invalid' => 'Invalid token',
    'provider_invalid' => 'Invalid provider',
    'login_success' => 'Login successful',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);