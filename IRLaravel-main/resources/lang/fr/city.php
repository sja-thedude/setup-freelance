<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'La ville a été récupérée avec succès.',
    'message_retrieved_list_successfully' => 'Les villes ont été récupérées avec succès.',
    'message_created_successfully' => 'La ville a été créée avec succès.',
    'message_updated_successfully' => 'La ville a été mise à jour avec succès.',
    'message_saved_successfully' => 'La ville a été enregistrée avec succès.',
    'message_deleted_successfully' => 'La ville a été supprimée avec succès.',
    'not_found' => 'Ville introuvable',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
