<?php

$originalTranslations = [
    'title' => 'Règles de TVA',
    'take_out' => 'À emporter',
    'delivery' => 'Livraison',
    'in_house' => 'Sur place',
    'save_success' => 'Les règles de TVA ont été enregistrées avec succès.',
    'name_holder' => 'Nom du taux de TVA',
    'message_retrieved_successfully' => 'La TVA a été récupérée avec succès',
    'message_retrieved_list_successfully' => 'La liste de TVA a été récupérée avec succès',
    'message_created_successfully' => 'La tâche de TVA a été créée avec succès',
    'message_updated_successfully' => 'La tâche de TVA a été mise à jour avec succès',
    'message_deleted_successfully' => 'La tâche de TVA a été supprimée avec succès',
    'message_saved_successfully' => 'La tâche de TVA a été enregistrée avec succès',
    'not_found' => 'TVA non trouvée',
    'id' => 'ID',

    'validation' => [
        'take_out_required' => 'À emporter est requis',
        'delivery_required' => 'Livraison est requise',
        'in_house_required' => 'Sur place est requis',
    ],

    'default_items' => [
        'prepared_dishes' => [
            'key' => 'prepared_dishes',
            'name' => 'Plats préparés',
        ],
        'dishes_without_preparation' => [
            'key' => 'dishes_without_preparation',
            'name' => 'Plats sans préparation',
        ],
        'alcoholic_beverages' => [
            'key' => 'alcoholic_beverages',
            'name' => 'Boissons alcoolisées',
        ],
        'non_alcoholic_beverages' => [
            'key' => 'non_alcoholic_beverages',
            'name' => 'Boissons non alcoolisées',
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);