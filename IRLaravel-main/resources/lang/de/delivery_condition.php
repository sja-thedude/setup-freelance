<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Die Lieferbedingung wurde erfolgreich abgerufen',
    'message_retrieved_list_successfully' => 'Die Lieferbedingungen wurden erfolgreich abgerufen',
    'not_found' => 'Lieferbedingung nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);