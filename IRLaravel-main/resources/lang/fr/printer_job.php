<?php

$originalTranslations = [
    'name' => 'Nom du travail d\'impression',
    'add_job' => 'Ajouter un travail d\'impression',
    'placeholder_search' => 'Rechercher un travail d\'impression',
    'deleted_confirm' => 'Le travail d\'impression a été supprimé',
    'message_retrieved_successfully' => 'Le travail d\'impression a été récupéré avec succès',
    'restaurant_list_retrieved_successfully' => 'La liste des restaurants a été récupérée avec succès',
    'message_created_successfully' => 'Le travail d\'impression a été créé avec succès',
    'message_updated_successfully' => 'Le travail d\'impression a été mis à jour avec succès',
    'message_deleted_successfully' => 'Le travail d\'impression a été supprimé avec succès',
    'message_saved_successfully' => 'Le travail d\'impression a été enregistré avec succès',
    'not_found' => 'Le travail d\'impression n\'a pas été trouvé',
    'color' => 'Couleur',
    'one_restaurant_join_one_job' => 'Un restaurant ne peut être que dans un seul travail d\'impression',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);