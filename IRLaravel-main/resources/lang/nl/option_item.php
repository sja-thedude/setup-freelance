<?php

$originalTranslations = [
    'created_successfully' => 'Optie-item is aangemaakt',
    'updated_successfully' => 'Optie-item is bijgewerkt',
    'deleted_successfully' => 'Optie-item is verwijderd',
    'message_retrieved_successfully' => 'Optie-item is succesvol opgehaald',
    'message_retrieved_multiple_successfully' => 'Optie-items zijn succesvol opgehaald',
    'not_found' => 'Optie-item niet gevonden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
