<?php

$originalTranslations = [
    'title' => 'Mobiele app',
    'description' => 'Kies uw gewenste look en feel.',
    'message_retrieved_list_successfully' => 'Werkruimte-apps zijn succesvol opgehaald',
    'message_retrieved_successfully' => 'Werkruimte-app is succesvol opgehaald',
    'message_created_successfully' => 'Werkruimte-app is succesvol aangemaakt.',
    'message_updated_successfully' => 'Werkruimte-app is succesvol bijgewerkt.',
    'message_saved_successfully' => 'Werkruimte-app is succesvol opgeslagen.',
    'message_deleted_successfully' => 'Werkruimte-app is succesvol verwijderd.',
    'message_updated_status_successfully' => 'App-instellingsstatus is succesvol bijgewerkt.',
    'message_created_setting_successfully' => 'App-instelling is succesvol aangemaakt.',
    'message_updated_setting_successfully' => 'App-instelling is succesvol bijgewerkt.',
    'message_deleted_setting_successfully' => 'App-instelling is succesvol verwijderd.',
    'not_found' => 'Werkruimte-app niet gevonden',

    'buttons' => [
        'new' => 'Nieuw'
    ],

    'theme' => [
        1 => '"Start bestelling" in primaire kleur',
        2 => '"Start bestelling" in het wit',
        3 => 'Mobiele app in donkere modus',
    ],

    'settings' => [
        'description' => 'Kies uw gewenste functies op het thuisscherm',
        'fields' => [
            'name' => 'naam',
            'title' => 'titel',
            'description' => 'beschrijving',
            'content' => 'inhoud',
            'url' => 'url',
        ],
        'placeholders' => [
            'name' => 'naam',
            'title' => 'Titel',
            'description' => 'Beschrijving',
            'content' => 'Inhoud',
            'url' => 'URL',
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
