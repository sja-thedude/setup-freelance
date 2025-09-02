<?php

$originalTranslations = [
    'created_successfully' => 'Créé avec succès',
    'created_confirm' => 'Le type d\'affaire a été créé.',
    'updated_successfully' => 'Mis à jour avec succès',
    'updated_confirm' => 'Le type d\'affaire a été mis à jour.',
    'deleted_successfully' => 'Supprimé avec succès',
    'deleted_confirm' => 'Le type d\'affaire a été supprimé.',
    'add' => 'Ajouter un type d\'affaire',
    'edit' => 'Modifier',
    'name' => 'Nom',
    'placeholder_search' => 'Rechercher un type d\'affaire',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
