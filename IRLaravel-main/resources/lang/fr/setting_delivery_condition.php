<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Le paramètre a été récupéré avec succès.',
    'message_retrieved_list_successfully' => 'Les paramètres ont été récupérés avec succès.',
    'message_created_successfully' => 'Le paramètre a été créé avec succès.',
    'message_updated_successfully' => 'Le paramètre a été mis à jour avec succès.',
    'message_saved_successfully' => 'Le paramètre a été enregistré avec succès.',
    'message_deleted_successfully' => 'Le paramètre a été supprimé avec succès.',
    'not_found' => 'Paramètre non trouvé',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
