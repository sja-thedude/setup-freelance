<?php

$originalTranslations = [
    'message_created_successfully' => 'Benachrichtigungsplan wurde erfolgreich erstellt.',
    'message_updated_successfully' => 'Benachrichtigungsplan wurde erfolgreich aktualisiert.',
    'message_saved_successfully' => 'Benachrichtigungsplan wurde erfolgreich gespeichert.',
    'message_deleted_successfully' => 'Benachrichtigungsplan wurde erfolgreich gelöscht.',
    'not_found' => 'Benachrichtigungsplan nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
