<?php

$originalTranslations = [
    'name' => 'Naam van de printergroep',
    'add_group' => 'Printergroep toevoegen',
    'placeholder_search' => 'Zoek printergroep',
    'deleted_confirm' => 'De printergroep is verwijderd',
    'message_retrieved_successfully' => 'De printergroep is succesvol opgehaald',
    'restaurant_list_retrieved_successfully' => 'De restaurantlijst is succesvol opgehaald',
    'not_found' => 'Printergroep niet gevonden',
    'color' => 'Kleur',
    'one_restaurant_join_one_group' => 'Een restaurant kan slechts in één printergroep zijn',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);