<?php

$originalTranslations = [
    'message_created_successfully' => 'Meldingsplan is succesvol aangemaakt.',
    'message_updated_successfully' => 'Meldingsplan is succesvol bijgewerkt.',
    'message_saved_successfully' => 'Meldingsplan is succesvol opgeslagen.',
    'message_deleted_successfully' => 'Meldingsplan is succesvol verwijderd.',
    'not_found' => 'Meldingsplan niet gevonden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
