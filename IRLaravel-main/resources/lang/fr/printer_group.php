<?php

$originalTranslations = [
    'name' => 'Nom du groupe d\'imprimantes',
    'add_group' => 'Ajouter un groupe d\'imprimantes',
    'placeholder_search' => 'Rechercher un groupe d\'imprimantes',
    'deleted_confirm' => 'Le groupe d\'imprimantes a été supprimé',
    'message_retrieved_successfully' => 'Le groupe d\'imprimantes a été récupéré avec succès',
    'restaurant_list_retrieved_successfully' => 'La liste des restaurants a été récupérée avec succès',
    'not_found' => 'Groupe d\'imprimantes introuvable',
    'color' => 'Couleur',
    'one_restaurant_join_one_group' => 'Un restaurant ne peut appartenir qu\'à un seul groupe d\'imprimantes',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);