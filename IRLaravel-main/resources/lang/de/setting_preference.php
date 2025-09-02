<?php

$originalTranslations = [
    'message_not_found' => 'Die Restaurantreferenzeinstellungen wurden nicht konfiguriert',
    'message_retrieved_list_successfully' => 'Einstellungsreferenzen wurden erfolgreich abgerufen',
    'message_retrieved_successfully' => 'Einstellungsreferenz wurde erfolgreich abgerufen',
    'message_created_successfully' => 'Einstellungsreferenz wurde erfolgreich erstellt',
    'message_updated_successfully' => 'Einstellungsreferenz wurde erfolgreich aktualisiert',
    'message_deleted_successfully' => 'Einstellungsreferenz wurde erfolgreich gelöscht',
    'message_saved_successfully' => 'Einstellungsreferenz wurde erfolgreich gespeichert',
    'not_found' => 'Einstellungsreferenz nicht gefunden',

    'default_items' => [
        /* key => values */
        'holiday_text' => [
            'key' => 'holiday_text',
            'name' => 'Feiertagstext',
            'title' => 'Freitext auf dem Startbildschirm anzeigen?',
            'content' => null,
        ],
        'table_ordering_pop_up_text' => [
            'key' => 'table_ordering_pop_up_text',
            'name' => 'Tischbestellung Pop-up Text',
            'title' => 'Freitext auf dem Startbildschirm bei Tischbestellungen anzeigen?',
            'content' => null,
        ],
        'self_ordering_pop_up_text' => [
            'key' => 'self_ordering_pop_up_text',
            'name' => 'Selbstbestellung Pop-up Text',
            'title' => 'Freitext auf dem Startbildschirm bei Kassenbestellungen anzeigen?',
            'content' => null,
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);