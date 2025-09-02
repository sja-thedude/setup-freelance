<?php

$originalTranslations = [
    'message_not_found' => 'De restaurantreferentie-instellingen zijn niet geconfigureerd',
    'message_retrieved_list_successfully' => 'Instellingsreferenties zijn succesvol opgehaald',
    'message_retrieved_successfully' => 'Instellingsreferentie is succesvol opgehaald',
    'message_created_successfully' => 'Instellingsreferentie is succesvol aangemaakt',
    'message_updated_successfully' => 'Instellingsreferentie is succesvol bijgewerkt',
    'message_deleted_successfully' => 'Instellingsreferentie is succesvol verwijderd',
    'message_saved_successfully' => 'Instellingsreferentie is succesvol opgeslagen',
    'not_found' => 'Instellingsreferentie niet gevonden',

    'default_items' => [
        /* key => values */
        'holiday_text' => [
            'key' => 'holiday_text',
            'name' => 'Vakantietekst',
            'title' => 'Vrije tekst tonen op beginscherm?',
            'content' => null,
        ],
        'table_ordering_pop_up_text' => [
            'key' => 'table_ordering_pop_up_text',
            'name' => 'Tafelbestelling pop-up tekst',
            'title' => 'Vrije tekst tonen op beginscherm bij ter plaatse eten?',
            'content' => null,
        ],
        'self_ordering_pop_up_text' => [
            'key' => 'self_ordering_pop_up_text',
            'name' => 'Zelfbestelling pop-up tekst',
            'title' => 'Vrije tekst tonen op beginscherm bij kassa bestellingen?',
            'content' => null,
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);