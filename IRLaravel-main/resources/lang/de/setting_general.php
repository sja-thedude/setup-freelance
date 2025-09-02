<?php

$originalTranslations = [
    'message_retrieved_list_successfully' => 'Einstellungs-Zeitslots wurden erfolgreich abgerufen',
    'message_retrieved_successfully' => 'Einstellungs-Zeitslot wurde erfolgreich abgerufen',
    'message_created_successfully' => 'Einstellungs-Zeitslot wurde erfolgreich erstellt',
    'message_updated_successfully' => 'Einstellungs-Zeitslot wurde erfolgreich aktualisiert',
    'message_deleted_successfully' => 'Einstellungs-Zeitslot wurde erfolgreich gelöscht',
    'message_saved_successfully' => 'Einstellungs-Zeitslot wurde erfolgreich gespeichert',
    'not_found' => 'Einstellungs-Zeitslot nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
