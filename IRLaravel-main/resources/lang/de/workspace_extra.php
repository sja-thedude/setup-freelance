<?php

$originalTranslations = [
    'message_created_successfully' => 'Workspace-Extra wurde erfolgreich erstellt.',
    'message_updated_successfully' => 'Workspace-Extra wurde erfolgreich aktualisiert.',
    'message_saved_successfully' => 'Workspace-Extra wurde erfolgreich gespeichert.',
    'message_deleted_successfully' => 'Workspace-Extra wurde erfolgreich gelöscht.',
    'not_found' => 'Workspace-Extra nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
