<?php

$originalTranslations = [
    'message_created_successfully' => 'La catégorie de l\'espace de travail a été créée avec succès.',
    'message_updated_successfully' => 'La catégorie de l\'espace de travail a été mise à jour avec succès.',
    'message_saved_successfully' => 'La catégorie de l\'espace de travail a été enregistrée avec succès.',
    'message_deleted_successfully' => 'La catégorie de l\'espace de travail a été supprimée avec succès.',
    'not_found' => 'Catégorie de l\'espace de travail non trouvée',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
