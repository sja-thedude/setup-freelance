<?php

$originalTranslations = [
    'message_created_successfully' => 'Benachrichtigungskategorie wurde erfolgreich erstellt.',
    'message_updated_successfully' => 'Benachrichtigungskategorie wurde erfolgreich aktualisiert.',
    'message_saved_successfully' => 'Benachrichtigungskategorie wurde erfolgreich gespeichert.',
    'message_deleted_successfully' => 'Benachrichtigungskategorie wurde erfolgreich gelöscht.',
    'not_found' => 'Benachrichtigungskategorie nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
