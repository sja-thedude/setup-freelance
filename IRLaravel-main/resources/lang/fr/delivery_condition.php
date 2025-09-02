<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'La condition de livraison a été récupérée avec succès',
    'message_retrieved_list_successfully' => 'Les conditions de livraison ont été récupérées avec succès',
    'not_found' => 'Condition de livraison non trouvée',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);