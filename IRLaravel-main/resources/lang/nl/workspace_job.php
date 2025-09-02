<?php

$originalTranslations = [
    'message_sent_successfully' => 'Bericht is succesvol verstuurd. Wij nemen zo snel mogelijk contact met u op.',
    'message_created_successfully' => 'Werkruimte-app is succesvol aangemaakt.',
    'message_updated_successfully' => 'Werkruimte-app is succesvol bijgewerkt.',
    'message_saved_successfully' => 'Werkruimte-app is succesvol opgeslagen.',
    'message_deleted_successfully' => 'Werkruimte-app is succesvol verwijderd.',
    'not_found' => 'Werkruimte-app niet gevonden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);