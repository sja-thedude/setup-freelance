<?php

$originalTranslations = [
    'not_found'         => 'Non trouvé',
    'success'           => 'Succès',
    'fail'              => 'Échec',
    'access_denied'     => 'Accès refusé',
    /* User domain */
    'user'              => [
        'not_found'                     => 'Utilisateur non trouvé',
        'created_successfully'          => 'L\'utilisateur a été créé',
        'updated_successfully'          => 'L\'utilisateur a été mis à jour',
        'deleted_successfully'          => 'L\'utilisateur a été supprimé',
        'change_password_successfully'  => 'Vous avez changé le mot de passe avec succès.',
        'updated_profile_successfully'  => 'Vous avez mis à jour votre profil avec succès.',
        'your_account_was_banned'       => 'Vous ne pouvez pas vous connecter car votre compte a été banni. Veuillez contacter l\'administrateur pour plus d\'informations !',
        'invalid_login_type'            => 'Type de connexion invalide',
        'verified_account_successfully' => 'Vous avez vérifié votre compte avec succès.',
        'invalid_current_password'      => 'Le mot de passe actuel est invalide',
        'changed_password_successfully' => 'Vous avez changé votre mot de passe avec succès',
        'changed_email_successfully'    => 'E-mail modifié avec succès.',
    ],
    'admin'             => [
        'created_successfully'         => 'Vous avez créé un nouvel utilisateur avec succès. Le mot de passe a été envoyé à son adresse e-mail.',
        'updated_successfully'         => 'L\'utilisateur a été mis à jour avec succès.',
        'deleted_successfully'         => 'L\'utilisateur a été supprimé',
        'profile_updated_successfully' => 'Votre profil a été mis à jour avec succès.',
    ],
    /* Role domain */
    'role'              => [
        'not_found'            => 'Rôle non trouvé',
        'created_successfully' => 'Le rôle a été créé',
        'updated_successfully' => 'Le rôle a été mis à jour',
        'deleted_successfully' => 'Le rôle a été supprimé',
    ],
    /* Country domain */
    'country'           => [
        'not_found'            => 'Pays non trouvé',
        'created_successfully' => 'Le pays a été créé',
        'updated_successfully' => 'Le pays a été mis à jour',
        'deleted_successfully' => 'Le pays a été supprimé',
    ],
    /* Banner domain */
    'banner'            => [
        'not_found'            => 'Bannière non trouvée',
        'created_successfully' => 'La bannière a été créée',
        'updated_successfully' => 'La bannière a été mise à jour',
        'deleted_successfully' => 'La bannière a été supprimée',
    ],
    /* Contact domain */
    'contact'           => [
        'not_found'            => 'Contact non trouvé',
        'created_successfully' => 'Merci de nous avoir envoyé votre message.',
    ],
    /* Post domain */
    'post'              => [
        'not_found'            => 'Article non trouvé',
        'created_successfully' => 'L\'article a été créé',
        'updated_successfully' => 'L\'article a été mis à jour',
        'deleted_successfully' => 'L\'article a été supprimé',
    ],
    /* Category domain */
    'category'          => [
        'not_found'            => 'Catégorie non trouvée',
        'created_successfully' => 'La catégorie a été créée',
        'updated_successfully' => 'La catégorie a été mise à jour',
        'deleted_successfully' => 'La catégorie a été supprimée',
    ],
    'lang'              => 'La langue a été enregistrée',
    'workspace_offline' => 'Ce restaurant n\'est pas en ligne, la commande ne peut pas être créée. Veuillez réessayer plus tard !',
    'upload_successfully' => 'Téléchargement réussi',
    'upload_fail' => 'Échec du téléchargement',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
