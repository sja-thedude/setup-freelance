<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'L\'adresse a été récupérée avec succès.',
    'message_retrieved_list_successfully' => 'Les adresses ont été récupérées avec succès.',
    'message_created_successfully' => 'L\'adresse a été créée avec succès.',
    'message_updated_successfully' => 'L\'adresse a été mise à jour avec succès.',
    'message_saved_successfully' => 'L\'adresse a été enregistrée avec succès.',
    'message_deleted_successfully' => 'L\'adresse a été supprimée avec succès.',
    'not_found' => 'Adresse introuvable',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
