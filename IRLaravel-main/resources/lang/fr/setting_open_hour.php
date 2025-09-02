<?php

$originalTranslations = [
    'title' => 'Heures d\'ouverture',
    'types' => [
        'takeout' => 'À emporter',
        'delivery' => 'Livraison',
        'in_house' => 'Sur place',
    ],
    'arrange_holiday' => 'Définir les vacances',
    'closed' => 'fermé',
    'holiday' => 'Vacances',
    'new_holiday' => '+ Nouvelle vacance',
    'note_holiday' => 'Message',
    'updated_success' => 'Votre paramètre a été mis à jour avec succès.',
    'updated_holiday_success' => 'Votre paramètre a été mis à jour avec succès.',
    'holiday_none' => 'Aucune vacance disponible. Définissez vos vacances pour fermer votre magasin pendant une certaine période.',
    'message_retrieved_successfully' => 'Le paramètre a été récupéré avec succès',
    'message_retrieved_list_successfully' => 'Les paramètres ont été récupérés avec succès',
    'from_until' => 'DE - JUSQU\'À',
    'text_opening_hours' => '!!Attention!! Si vous modifiez les heures d\'ouverture d\'un jour de la semaine, les paramètres de créneaux horaires dynamiques pour ce jour seront automatiquement réinitialisés!'
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);