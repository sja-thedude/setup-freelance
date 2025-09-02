<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Land is succesvol opgehaald.',
    'message_retrieved_list_successfully' => 'Landen zijn succesvol opgehaald.',
    'message_created_successfully' => 'Land is succesvol aangemaakt.',
    'message_updated_successfully' => 'Land is succesvol bijgewerkt.',
    'message_saved_successfully' => 'Land is succesvol opgeslagen.',
    'message_deleted_successfully' => 'Land is succesvol verwijderd.',
    'not_found' => 'Land niet gevonden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
