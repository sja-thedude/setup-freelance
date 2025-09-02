<?php

$originalTranslations = [
    'Administrator'         => 'Administrator',
    'User'                  => 'User',
    'backoffice_role_title' => 'Roles',
    'client_role_title'     => 'Client roles',
    'add_role'              => 'Add role',
    'edit_role'             => 'Edit role',
    'detail_role'           => 'Detail role',
    'are_you_sure_delete'   => 'Are you sure you want to delete this role?',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);