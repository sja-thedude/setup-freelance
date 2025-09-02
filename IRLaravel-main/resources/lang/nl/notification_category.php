<?php

$originalTranslations = [
    'message_created_successfully' => 'Notificatiecategorie is succesvol aangemaakt.',
    'message_updated_successfully' => 'Notificatiecategorie is succesvol bijgewerkt.',
    'message_saved_successfully' => 'Notificatiecategorie is succesvol opgeslagen.',
    'message_deleted_successfully' => 'Notificatiecategorie is succesvol verwijderd.',
    'not_found' => 'Notificatiecategorie niet gevonden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
