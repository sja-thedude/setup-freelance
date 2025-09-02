<?php

$originalTranslations = [
    'message_created_successfully' => 'L\'espace de travail supplémentaire a été créé avec succès.',
    'message_updated_successfully' => 'L\'espace de travail supplémentaire a été mis à jour avec succès.',
    'message_saved_successfully' => 'L\'espace de travail supplémentaire a été enregistré avec succès.',
    'message_deleted_successfully' => 'L\'espace de travail supplémentaire a été supprimé avec succès.',
    'not_found' => 'Espace de travail supplémentaire non trouvé',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
