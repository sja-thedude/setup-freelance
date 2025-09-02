<?php

$originalTranslations = [
    'name' => 'Gruppenname',
    'add_group' => 'Gruppe hinzufügen',
    'placeholder_search' => 'Gruppe suchen',
    'deleted_confirm' => 'Die Gruppe wurde gelöscht',
    'message_retrieved_successfully' => 'Die Gruppe wurde erfolgreich abgerufen',
    'restaurant_list_retrieved_successfully' => 'Die Restaurantliste wurde erfolgreich abgerufen',
    'not_found' => 'Gruppe nicht gefunden',
    'color' => 'Farbe',

    'default_items' => [
        'name' => [
            'key' => 'name',
            'name' => null,
            'description' => null,
        ],
        'description' => [
            'key' => 'description',
            'name' => null,
            'description' => null,
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);