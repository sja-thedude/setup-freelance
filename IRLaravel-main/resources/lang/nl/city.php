<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Stad is succesvol opgehaald.',
    'message_retrieved_list_successfully' => 'Steden zijn succesvol opgehaald.',
    'message_created_successfully' => 'Stad is succesvol aangemaakt.',
    'message_updated_successfully' => 'Stad is succesvol bijgewerkt.',
    'message_saved_successfully' => 'Stad is succesvol opgeslagen.',
    'message_deleted_successfully' => 'Stad is succesvol verwijderd.',
    'not_found' => 'Stad niet gevonden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
