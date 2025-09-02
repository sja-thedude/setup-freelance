<?php

$originalTranslations = [
    'Administrator'         => 'Administrator',
    'User'                  => 'Benutzer',
    'backoffice_role_title' => 'Rollen',
    'client_role_title'     => 'Kundenrollen',
    'add_role'              => 'Rolle hinzufügen',
    'edit_role'             => 'Rolle bearbeiten',
    'detail_role'           => 'Rolle im Detail',
    'are_you_sure_delete'   => 'Sind Sie sicher, dass Sie diese Rolle löschen möchten?',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);