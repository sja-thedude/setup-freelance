<?php

$originalTranslations = [
    'created_successfully' => 'Created successfully',
    'created_confirm' => 'The type zaak has been created.',
    'updated_successfully' => 'Updated successfully',
    'updated_confirm' => 'The type zaak has been updated.',
    'deleted_successfully' => 'Deleted successfully',
    'deleted_confirm' => 'The type zaak has been deleted.',
    'add' => 'Add type zaak',
    'edit' => 'Edit',
    'name' => 'Name',
    'placeholder_search' => 'Search for type zaak',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
