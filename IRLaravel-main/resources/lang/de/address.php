<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Adresse wurde erfolgreich abgerufen.',
    'message_retrieved_list_successfully' => 'Adressen wurden erfolgreich abgerufen.',
    'message_created_successfully' => 'Adresse wurde erfolgreich erstellt.',
    'message_updated_successfully' => 'Adresse wurde erfolgreich aktualisiert.',
    'message_saved_successfully' => 'Adresse wurde erfolgreich gespeichert.',
    'message_deleted_successfully' => 'Adresse wurde erfolgreich gelöscht.',
    'not_found' => 'Adresse nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
