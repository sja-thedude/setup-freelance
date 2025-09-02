<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Einstellung wurde erfolgreich abgerufen.',
    'message_retrieved_list_successfully' => 'Einstellungen wurden erfolgreich abgerufen.',
    'message_created_successfully' => 'Einstellung wurde erfolgreich erstellt.',
    'message_updated_successfully' => 'Einstellung wurde erfolgreich aktualisiert.',
    'message_saved_successfully' => 'Einstellung wurde erfolgreich gespeichert.',
    'message_deleted_successfully' => 'Einstellung wurde erfolgreich gelöscht.',
    'not_found' => 'Einstellung nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
