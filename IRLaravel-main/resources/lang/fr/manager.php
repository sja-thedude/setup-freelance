<?php

$originalTranslations = [
    'created_successfully' => 'Le gestionnaire a été créé',
    'updated_successfully' => 'Le gestionnaire a été mis à jour',
    'deleted_successfully' => 'Supprimé avec succès',
    'deleted_confirm' => 'Ce gestionnaire de compte a été supprimé avec succès.',
    'not_found' => 'Gestionnaire introuvable',

    'add_manager' => 'Nouveau gestionnaire de compte',
    'edit_manager' => 'Modifier le gestionnaire de compte',
    'detail_manager' => 'Détails du gestionnaire de compte',
    'are_you_sure_delete' => 'Êtes-vous sûr de vouloir supprimer <strong>:name</strong> ?',

    'title' => 'Gestionnaires de compte',
    'name' => 'Nom',
    'email' => 'E-mail',
    'gsm' => 'Mobile',
    'status' => 'Statut',
    'active_date' => 'Date d\'activation',
    'actions' => 'Actions',
    'status_0' => 'Actif',
    'status_1' => 'Invitation expirée',
    'status_2' => 'Invitation envoyée',
    'send_invitation' => 'Envoyer l\'invitation',
    'choose_another_manager' => 'D\'abord, assignez les clients à un autre gestionnaire de compte !',
    'delete_account_manager' => 'Supprimer le gestionnaire de compte',
    'account_manager' => 'Gestionnaire de compte',
    'resend_invitation' => 'Renvoyer l\'invitation',
    'reset_invitation_confirm' => 'Êtes-vous sûr de vouloir renvoyer une invitation ?',
    'send_invitation_subject' => 'Bienvenue chez It\'s Ready',
    'sent_invitation' => 'Vous avez envoyé l\'invitation avec succès.',
    'sent_invitation_success' => 'Envoyé avec succès',
    'first_name' => 'Prénom',
    'last_name' => 'Nom',
    'edit_profile' => 'Modifier le profil',
    'change_password' => 'Changer le mot de passe',
    'add_account_manager' => 'Ajouter un gestionnaire de compte',
    'submit_new_account_manager' => 'Envoyer l\'invitation',

    'validation' => [
        'name_required' => 'Le nom est requis',
        'email_required' => 'L\'e-mail est invalide'
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);