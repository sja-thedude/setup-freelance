<?php

$originalTranslations = [
    'message_created_successfully' => 'Druckauftragseinstellung wurde erfolgreich erstellt',
    'message_updated_successfully' => 'Druckauftragseinstellung wurde erfolgreich aktualisiert',
    'message_deleted_successfully' => 'Druckauftragseinstellung wurde erfolgreich gelöscht',
    'message_saved_successfully' => 'Druckauftragseinstellung wurde erfolgreich gespeichert',
    'not_found' => 'Druckauftragseinstellung nicht gefunden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
