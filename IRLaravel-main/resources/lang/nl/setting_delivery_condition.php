<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Instelling is succesvol opgehaald.',
    'message_retrieved_list_successfully' => 'Instellingen zijn succesvol opgehaald.',
    'message_created_successfully' => 'Instelling is succesvol aangemaakt.',
    'message_updated_successfully' => 'Instelling is succesvol bijgewerkt.',
    'message_saved_successfully' => 'Instelling is succesvol opgeslagen.',
    'message_deleted_successfully' => 'Instelling is succesvol verwijderd.',
    'not_found' => 'Instelling niet gevonden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
