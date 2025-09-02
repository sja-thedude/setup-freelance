<?php

$originalTranslations = [
    'message_sent_successfully' => 'Nachricht wurde erfolgreich gesendet. Wir werden uns so schnell wie möglich mit Ihnen in Verbindung setzen.',
    'message_created_successfully' => 'Workspace-App wurde erfolgreich erstellt.',
    'message_updated_successfully' => 'Workspace-App wurde erfolgreich aktualisiert.',
    'message_saved_successfully' => 'Workspace-App wurde erfolgreich gespeichert.',
    'message_deleted_successfully' => 'Workspace-App wurde erfolgreich gelöscht.',
    'not_found' => 'Workspace-App nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);