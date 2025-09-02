<?php

$originalTranslations = [
    'title' => 'BTW regels',
    'take_out' => 'Afhaal',
    'delivery' => 'Levering',
    'in_house' => 'Ter plaatse',
    'save_success' => 'BTW regels zijn succesvol opgeslagen.',
    'name_holder' => 'Naam van BTW-tarief',
    'message_retrieved_successfully' => 'BTW is succesvol opgehaald',
    'message_retrieved_list_successfully' => 'BTW-lijst is succesvol opgehaald',
    'message_created_successfully' => 'BTW-taak is succesvol aangemaakt',
    'message_updated_successfully' => 'BTW-taak is succesvol bijgewerkt',
    'message_deleted_successfully' => 'BTW-taak is succesvol verwijderd',
    'message_saved_successfully' => 'BTW-taak is succesvol opgeslagen',
    'not_found' => 'BTW niet gevonden',
    'id' => 'ID',

    'validation' => [
        'take_out_required' => 'Afhaal is verplicht',
        'delivery_required' => 'Levering is verplicht',
        'in_house_required' => 'Ter plaatse is verplicht',
    ],

    'default_items' => [
        'prepared_dishes' => [
            'key' => 'prepared_dishes',
            'name' => 'Bereide gerechten',
        ],
        'dishes_without_preparation' => [
            'key' => 'dishes_without_preparation',
            'name' => 'Gerechten zonder bereiding',
        ],
        'alcoholic_beverages' => [
            'key' => 'alcoholic_beverages',
            'name' => 'Alcoholische dranken',
        ],
        'non_alcoholic_beverages' => [
            'key' => 'non_alcoholic_beverages',
            'name' => 'Non-alcoholische dranken',
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);