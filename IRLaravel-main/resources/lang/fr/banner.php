<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'La bannière a été récupérée avec succès.',
    'message_retrieved_list_successfully' => 'Les bannières ont été récupérées avec succès.',
    'message_created_successfully' => 'La bannière a été créée avec succès.',
    'message_updated_successfully' => 'La bannière a été mise à jour avec succès.',
    'message_saved_successfully' => 'La bannière a été enregistrée avec succès.',
    'message_deleted_successfully' => 'La bannière a été supprimée avec succès.',
    'not_found' => 'Bannière introuvable',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
