<?php

$originalTranslations = [
    'title' => 'Mobile App',
    'description' => 'Wählen Sie Ihr gewünschtes Aussehen und Gefühl.',
    'message_retrieved_list_successfully' => 'Workspace-Apps wurden erfolgreich abgerufen',
    'message_retrieved_successfully' => 'Workspace-App wurde erfolgreich abgerufen',
    'message_created_successfully' => 'Workspace-App wurde erfolgreich erstellt.',
    'message_updated_successfully' => 'Workspace-App wurde erfolgreich aktualisiert.',
    'message_saved_successfully' => 'Workspace-App wurde erfolgreich gespeichert.',
    'message_deleted_successfully' => 'Workspace-App wurde erfolgreich gelöscht.',
    'message_updated_status_successfully' => 'App-Einstellungsstatus wurde erfolgreich aktualisiert.',
    'message_created_setting_successfully' => 'App-Einstellung wurde erfolgreich erstellt.',
    'message_updated_setting_successfully' => 'App-Einstellung wurde erfolgreich aktualisiert.',
    'message_deleted_setting_successfully' => 'App-Einstellung wurde erfolgreich gelöscht.',
    'not_found' => 'Workspace-App nicht gefunden',

    'buttons' => [
        'new' => 'Neu'
    ],

    'theme' => [
        1 => '"Bestellung starten" in Primärfarbe',
        2 => '"Bestellung starten" in Weiß',
        3 => 'Mobile App im Dunkelmodus',
    ],

    'settings' => [
        'description' => 'Wählen Sie Ihre gewünschten Funktionen auf dem Startbildschirm',
        'fields' => [
            'name' => 'Name',
            'title' => 'Titel',
            'description' => 'Beschreibung',
            'content' => 'Inhalt',
            'url' => 'URL',
        ],
        'placeholders' => [
            'name' => 'Name',
            'title' => 'Titel',
            'description' => 'Beschreibung',
            'content' => 'Inhalt',
            'url' => 'URL',
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
