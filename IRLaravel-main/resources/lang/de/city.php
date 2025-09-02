<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Stadt wurde erfolgreich abgerufen.',
    'message_retrieved_list_successfully' => 'Städte wurden erfolgreich abgerufen.',
    'message_created_successfully' => 'Stadt wurde erfolgreich erstellt.',
    'message_updated_successfully' => 'Stadt wurde erfolgreich aktualisiert.',
    'message_saved_successfully' => 'Stadt wurde erfolgreich gespeichert.',
    'message_deleted_successfully' => 'Stadt wurde erfolgreich gelöscht.',
    'not_found' => 'Stadt nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
