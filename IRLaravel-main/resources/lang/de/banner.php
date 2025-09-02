<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Banner wurde erfolgreich abgerufen.',
    'message_retrieved_list_successfully' => 'Banner wurden erfolgreich abgerufen.',
    'message_created_successfully' => 'Banner wurde erfolgreich erstellt.',
    'message_updated_successfully' => 'Banner wurde erfolgreich aktualisiert.',
    'message_saved_successfully' => 'Banner wurde erfolgreich gespeichert.',
    'message_deleted_successfully' => 'Banner wurde erfolgreich gelöscht.',
    'not_found' => 'Banner nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
