<?php

$originalTranslations = [
    'name' => 'Groepsnaam',
    'add_group' => 'Groep toevoegen',
    'placeholder_search' => 'Zoek groep',
    'deleted_confirm' => 'De groep is verwijderd',
    'message_retrieved_successfully' => 'De groep is succesvol opgehaald',
    'restaurant_list_retrieved_successfully' => 'De restaurantlijst is succesvol opgehaald',
    'not_found' => 'Groep niet gevonden',
    'color' => 'Kleur',

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