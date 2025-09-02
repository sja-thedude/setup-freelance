<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Adres is succesvol opgehaald.',
    'message_retrieved_list_successfully' => 'Adressen zijn succesvol opgehaald.',
    'message_created_successfully' => 'Adres is succesvol aangemaakt.',
    'message_updated_successfully' => 'Adres is succesvol bijgewerkt.',
    'message_saved_successfully' => 'Adres is succesvol opgeslagen.',
    'message_deleted_successfully' => 'Adres is succesvol verwijderd.',
    'not_found' => 'Adres niet gevonden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
