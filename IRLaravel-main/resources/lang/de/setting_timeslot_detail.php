<?php

$originalTranslations = [
    'message_retrieved_list_successfully' => 'Einstellungen der Zeitfensterdetails wurden erfolgreich abgerufen',
    'message_retrieved_successfully' => 'Einstellung des Zeitfensterdetails wurde erfolgreich abgerufen',
    'message_created_successfully' => 'Einstellung des Zeitfensterdetails wurde erfolgreich erstellt',
    'message_updated_successfully' => 'Einstellung des Zeitfensterdetails wurde erfolgreich aktualisiert',
    'message_deleted_successfully' => 'Einstellung des Zeitfensterdetails wurde erfolgreich gelöscht',
    'message_saved_successfully' => 'Einstellung des Zeitfensterdetails wurde erfolgreich gespeichert',
    'not_found' => 'Einstellung des Zeitfensterdetails nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
