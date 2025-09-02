<?php

$originalTranslations = [
    'name' => 'Druckauftrag Name',
    'add_job' => 'Druckauftrag hinzufügen',
    'placeholder_search' => 'Druckauftrag suchen',
    'deleted_confirm' => 'Der Druckauftrag wurde gelöscht',
    'message_retrieved_successfully' => 'Der Druckauftrag wurde erfolgreich abgerufen',
    'restaurant_list_retrieved_successfully' => 'Die Restaurantliste wurde erfolgreich abgerufen',
    'message_created_successfully' => 'Der Druckauftrag wurde erfolgreich erstellt',
    'message_updated_successfully' => 'Der Druckauftrag wurde erfolgreich aktualisiert',
    'message_deleted_successfully' => 'Der Druckauftrag wurde erfolgreich gelöscht',
    'message_saved_successfully' => 'Der Druckauftrag wurde erfolgreich gespeichert',
    'not_found' => 'Druckauftrag nicht gefunden',
    'color' => 'Farbe',
    'one_restaurant_join_one_job' => 'Ein Restaurant kann nur in einem Druckauftrag sein',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);