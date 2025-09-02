<?php

$originalTranslations = [
    'token_invalid' => 'Jeton invalide',
    'provider_invalid' => 'Fournisseur invalide',
    'login_success' => 'Succès',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);