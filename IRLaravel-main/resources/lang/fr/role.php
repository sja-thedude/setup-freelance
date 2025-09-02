<?php

$originalTranslations = [
    'Administrator'         => 'Administrateur',
    'User'                  => 'Utilisateur',
    'backoffice_role_title' => 'Rôles',
    'client_role_title'     => 'Rôles des clients',
    'add_role'              => 'Ajouter un rôle',
    'edit_role'             => 'Modifier le rôle',
    'detail_role'           => 'Détail du rôle',
    'are_you_sure_delete'   => 'Êtes-vous sûr de vouloir supprimer ce rôle?',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);