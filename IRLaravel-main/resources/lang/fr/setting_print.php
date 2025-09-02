<?php

$originalTranslations = [
    'message_created_successfully' => 'Le travail d\'impression a été créé avec succès',
    'message_updated_successfully' => 'Le travail d\'impression a été mis à jour avec succès',
    'message_deleted_successfully' => 'Le travail d\'impression a été supprimé avec succès',
    'message_saved_successfully' => 'Le travail d\'impression a été enregistré avec succès',
    'not_found' => 'Travail d\'impression non trouvé',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
