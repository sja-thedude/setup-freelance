<?php

$originalTranslations = [
    'message_invalid_token' => 'Restaurant hat kein Zahlungstoken eingerichtet',
    'message_retrieved_list_successfully' => 'Zahlungseinstellungen wurden erfolgreich abgerufen',
    'message_retrieved_successfully' => 'Zahlungseinstellung wurde erfolgreich abgerufen',
    'message_created_successfully' => 'Zahlungseinstellung wurde erfolgreich erstellt',
    'message_updated_successfully' => 'Zahlungseinstellung wurde erfolgreich aktualisiert',
    'message_deleted_successfully' => 'Zahlungseinstellung wurde erfolgreich gelöscht',
    'message_saved_successfully' => 'Zahlungseinstellung wurde erfolgreich gespeichert',
    'not_found' => 'Zahlungseinstellung nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);