<?php

$originalTranslations = [
    'name' => 'Name der Druckergruppe',
    'add_group' => 'Druckergruppe hinzufügen',
    'placeholder_search' => 'Druckergruppe suchen',
    'deleted_confirm' => 'Die Druckergruppe wurde gelöscht',
    'message_retrieved_successfully' => 'Die Druckergruppe wurde erfolgreich abgerufen',
    'restaurant_list_retrieved_successfully' => 'Die Restaurantliste wurde erfolgreich abgerufen',
    'not_found' => 'Druckergruppe nicht gefunden',
    'color' => 'Farbe',
    'one_restaurant_join_one_group' => 'Ein Restaurant kann nur in einer Druckergruppe sein',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);