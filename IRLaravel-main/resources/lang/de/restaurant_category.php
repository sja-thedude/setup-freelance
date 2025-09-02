<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Restaurantkategorie wurde erfolgreich abgerufen',
    'message_retrieved_list_successfully' => 'Restaurantkategorien wurden erfolgreich abgerufen',
    'message_created_successfully' => 'Restaurantkategorie wurde erfolgreich erstellt',
    'message_updated_successfully' => 'Restaurantkategorie wurde erfolgreich aktualisiert',
    'message_deleted_successfully' => 'Restaurantkategorie wurde erfolgreich gelöscht',
    'message_saved_successfully' => 'Restaurantkategorie wurde erfolgreich gespeichert',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
