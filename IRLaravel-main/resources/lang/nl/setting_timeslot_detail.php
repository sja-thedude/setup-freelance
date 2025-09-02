<?php

$originalTranslations = [
    'message_retrieved_list_successfully' => 'Instelling tijdslotdetails zijn succesvol opgehaald',
    'message_retrieved_successfully' => 'Instelling tijdslotdetail is succesvol opgehaald',
    'message_created_successfully' => 'Instelling tijdslotdetail is succesvol aangemaakt',
    'message_updated_successfully' => 'Instelling tijdslotdetail is succesvol bijgewerkt',
    'message_deleted_successfully' => 'Instelling tijdslotdetail is succesvol verwijderd',
    'message_saved_successfully' => 'Instelling tijdslotdetail is succesvol opgeslagen',
    'not_found' => 'Instelling tijdslotdetail niet gevonden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
