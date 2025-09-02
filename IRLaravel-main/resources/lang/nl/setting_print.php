<?php

$originalTranslations = [
    'message_created_successfully' => 'Instelling afdruktaak is succesvol aangemaakt',
    'message_updated_successfully' => 'Instelling afdruktaak is succesvol bijgewerkt',
    'message_deleted_successfully' => 'Instelling afdruktaak is succesvol verwijderd',
    'message_saved_successfully' => 'Instelling afdruktaak is succesvol opgeslagen',
    'not_found' => 'Instelling afdruk niet gevonden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
