<?php

$originalTranslations = [
    'created_successfully' => 'Erfolgreich erstellt',
    'created_confirm' => 'Der Typ Zaak wurde erstellt.',
    'updated_successfully' => 'Erfolgreich aktualisiert',
    'updated_confirm' => 'Der Typ Zaak wurde aktualisiert.',
    'deleted_successfully' => 'Erfolgreich gelöscht',
    'deleted_confirm' => 'Der Typ Zaak wurde gelöscht.',
    'add' => 'Typ Zaak hinzufügen',
    'edit' => 'Bearbeiten',
    'name' => 'Name',
    'placeholder_search' => 'Nach Typ Zaak suchen',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
