<?php

$originalTranslations = [
    'message_retrieved_list_successfully' => 'Arbeitsbereich-App-Metadaten wurden erfolgreich abgerufen',
    'message_retrieved_successfully' => 'Arbeitsbereich-App-Metadaten wurden erfolgreich abgerufen',
    'message_created_successfully' => 'Arbeitsbereich-App-Metadaten wurden erfolgreich erstellt.',
    'message_updated_successfully' => 'Arbeitsbereich-App-Metadaten wurden erfolgreich aktualisiert.',
    'message_saved_successfully' => 'Arbeitsbereich-App-Metadaten wurden erfolgreich gespeichert.',
    'message_deleted_successfully' => 'Arbeitsbereich-App-Metadaten wurden erfolgreich gelöscht.',
    'not_found' => 'Arbeitsbereich-App-Metadaten nicht gefunden',

    'default_items' => [
        /* key => values */
        'reserve' => [
            'key' => 'reserve',
            'name' => 'Reservieren',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'reviews' => [
            'key' => 'reviews',
            'name' => 'Bewertungen',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'route' => [
            'key' => 'route',
            'name' => 'Route',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'jobs' => [
            'key' => 'jobs',
            'name' => 'Jobs',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'recent' => [
            'key' => 'recent',
            'name' => 'Kürzlich',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'favorites' => [
            'key' => 'favorites',
            'name' => 'Favoriten',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'account' => [
            'key' => 'account',
            'name' => 'Konto',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'share' => [
            'key' => 'share',
            'name' => 'Teilen',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'loyalty' => [
            'key' => 'loyalty',
            'name' => 'Treuekarte',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'menu' => [
            'key' => 'menu',
            'name' => 'Speisekarte',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
