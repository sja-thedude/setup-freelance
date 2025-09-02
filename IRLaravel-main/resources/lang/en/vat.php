<?php

$originalTranslations = [
    'title' => 'VAT rules',
    'take_out' => 'Take out',
    'delivery' => 'Delivery',
    'in_house' => 'On site',
    'save_success' => 'VAT rules have been saved successfully.',
    'name_holder' => 'Name of VAT rate',
    'message_retrieved_successfully' => 'VAT has been retrieved successfully',
    'message_retrieved_list_successfully' => 'VAT list has been retrieved successfully',
    'message_created_successfully' => 'VAT job has been created successfully',
    'message_updated_successfully' => 'VAT job has been updated successfully',
    'message_deleted_successfully' => 'VAT job has been deleted successfully',
    'message_saved_successfully' => 'VAT job has been saved successfully',
    'not_found' => 'VAT not found',
    'id' => 'ID',

    'validation' => [
        'take_out_required' => 'Take out is required',
        'delivery_required' => 'Delivery is required',
        'in_house_required' => 'On site is required',
    ],

    'default_items' => [
        'prepared_dishes' => [
            'key' => 'prepared_dishes',
            'name' => 'Prepared dishes',
        ],
        'dishes_without_preparation' => [
            'key' => 'dishes_without_preparation',
            'name' => 'Dishes without preparation',
        ],
        'alcoholic_beverages' => [
            'key' => 'alcoholic_beverages',
            'name' => 'Alcoholic beverages',
        ],
        'non_alcoholic_beverages' => [
            'key' => 'non_alcoholic_beverages',
            'name' => 'Non-alcoholic beverages',
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);