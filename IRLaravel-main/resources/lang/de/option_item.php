<?php

$originalTranslations = [
    'created_successfully' => 'Optionselement wurde erstellt',
    'updated_successfully' => 'Optionselement wurde aktualisiert',
    'deleted_successfully' => 'Optionselement wurde gelöscht',
    'message_retrieved_successfully' => 'Optionselement wurde erfolgreich abgerufen',
    'message_retrieved_multiple_successfully' => 'Optionselemente wurden erfolgreich abgerufen',
    'not_found' => 'Optionselement nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
