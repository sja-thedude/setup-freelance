<?php

$originalTranslations = [
    'message_created_successfully' => 'Werkruimte extra is succesvol aangemaakt.',
    'message_updated_successfully' => 'Werkruimte extra is succesvol bijgewerkt.',
    'message_saved_successfully' => 'Werkruimte extra is succesvol opgeslagen.',
    'message_deleted_successfully' => 'Werkruimte extra is succesvol verwijderd.',
    'not_found' => 'Werkruimte extra niet gevonden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
