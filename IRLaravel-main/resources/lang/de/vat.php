<?php

$originalTranslations = [
    'title' => 'MwSt-Regeln',
    'take_out' => 'Mitnahme',
    'delivery' => 'Lieferung',
    'in_house' => 'Vor Ort',
    'save_success' => 'MwSt-Regeln wurden erfolgreich gespeichert.',
    'name_holder' => 'Name des Mehrwertsteuersatzes',
    'message_retrieved_successfully' => 'MwSt wurde erfolgreich abgerufen',
    'message_retrieved_list_successfully' => 'MwSt-Liste wurde erfolgreich abgerufen',
    'message_created_successfully' => 'MwSt-Aufgabe wurde erfolgreich erstellt',
    'message_updated_successfully' => 'MwSt-Aufgabe wurde erfolgreich aktualisiert',
    'message_deleted_successfully' => 'MwSt-Aufgabe wurde erfolgreich gelöscht',
    'message_saved_successfully' => 'MwSt-Aufgabe wurde erfolgreich gespeichert',
    'not_found' => 'MwSt nicht gefunden',
    'id' => 'ID',

    'validation' => [
        'take_out_required' => 'Mitnahme ist erforderlich',
        'delivery_required' => 'Lieferung ist erforderlich',
        'in_house_required' => 'Vor Ort ist erforderlich',
    ],

    'default_items' => [
        'prepared_dishes' => [
            'key' => 'prepared_dishes',
            'name' => 'Zubereitete Gerichte',
        ],
        'dishes_without_preparation' => [
            'key' => 'dishes_without_preparation',
            'name' => 'Gerichte ohne Zubereitung',
        ],
        'alcoholic_beverages' => [
            'key' => 'alcoholic_beverages',
            'name' => 'Alkoholische Getränke',
        ],
        'non_alcoholic_beverages' => [
            'key' => 'non_alcoholic_beverages',
            'name' => 'Nicht-alkoholische Getränke',
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);