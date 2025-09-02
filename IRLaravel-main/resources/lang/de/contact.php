<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Kontakt wurde erfolgreich abgerufen.',
    'message_retrieved_list_successfully' => 'Kontakte wurden erfolgreich abgerufen.',
    'message_created_successfully' => 'Kontakt wurde erfolgreich erstellt.',
    'message_updated_successfully' => 'Kontakt wurde erfolgreich aktualisiert.',
    'message_saved_successfully' => 'Kontakt wurde erfolgreich gespeichert.',
    'message_deleted_successfully' => 'Kontakt wurde erfolgreich gelöscht.',
    'not_found' => 'Kontakt nicht gefunden',
    'back' => 'Zurück',
    'fill_info' => 'Bitte füllen Sie die folgenden Informationen aus, wenn Sie als <b>Firma</b>, <b>Gruppe</b> oder <b>Klasse</b> bestellen möchten.<br> Wir werden uns so schnell wie möglich mit Ihnen in Verbindung setzen!',
    'name_and_surname' => 'Name und Vorname',
    'phone' => 'Telefon/Handy',
    'address' => 'Ort',
    'send' => 'SENDEN',
    'company' => 'Firma',
    'email' => 'E-Mail-Adresse',
    'message' => 'Nachricht',
    'message_sent_successfully' => 'Nachricht wurde erfolgreich gesendet. Wir werden uns so schnell wie möglich mit Ihnen in Verbindung setzen.',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);