<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'De afleverconditie is succesvol opgehaald',
    'message_retrieved_list_successfully' => 'De leveringsvoorwaarden zijn succesvol opgehaald',
    'not_found' => 'Leveringsvoorwaarde niet gevonden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);