<?php

$originalTranslations = [
    'created_successfully' => 'Succesvol aangemaakt',
    'created_confirm' => 'Het type zaak is aangemaakt.',
    'updated_successfully' => 'Succesvol bijgewerkt',
    'updated_confirm' => 'Het type zaak is bijgewerkt.',
    'deleted_successfully' => 'Succesvol verwijderd',
    'deleted_confirm' => 'Het type zaak is verwijderd.',
    'add'       => 'Voeg type zaak toe',
    'edit' => 'Bewerken',
    'name' => 'Naam',
    'placeholder_search' => 'Zoek naar type zaak',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
