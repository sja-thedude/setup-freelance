<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Restaurantcategorie is succesvol opgehaald',
    'message_retrieved_list_successfully' => 'Restaurantcategorieën zijn succesvol opgehaald',
    'message_created_successfully' => 'Restaurantcategorie is succesvol aangemaakt',
    'message_updated_successfully' => 'Restaurantcategorie is succesvol bijgewerkt',
    'message_deleted_successfully' => 'Restaurantcategorie is succesvol verwijderd',
    'message_saved_successfully' => 'Restaurantcategorie is succesvol opgeslagen',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
