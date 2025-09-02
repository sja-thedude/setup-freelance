<?php

$originalTranslations = [
    'message_created_successfully' => 'Arbeitsbereichskategorie wurde erfolgreich erstellt.',
    'message_updated_successfully' => 'Arbeitsbereichskategorie wurde erfolgreich aktualisiert.',
    'message_saved_successfully' => 'Arbeitsbereichskategorie wurde erfolgreich gespeichert.',
    'message_deleted_successfully' => 'Arbeitsbereichskategorie wurde erfolgreich gelöscht.',
    'not_found' => 'Arbeitsbereichskategorie nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
