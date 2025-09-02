<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Das Land wurde erfolgreich abgerufen.',
    'message_retrieved_list_successfully' => 'Die Länder wurden erfolgreich abgerufen.',
    'message_created_successfully' => 'Das Land wurde erfolgreich erstellt.',
    'message_updated_successfully' => 'Das Land wurde erfolgreich aktualisiert.',
    'message_saved_successfully' => 'Das Land wurde erfolgreich gespeichert.',
    'message_deleted_successfully' => 'Das Land wurde erfolgreich gelöscht.',
    'not_found' => 'Land nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
