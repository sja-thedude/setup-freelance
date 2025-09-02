<?php

$originalTranslations = [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed'   => 'Combinaison invalide ! Veuillez réessayer.',
    'throttle' => 'Trop de tentatives de connexion. Veuillez réessayer dans :seconds secondes.',
    'banned'   => 'Votre compte a été désactivé par les administrateurs. Veuillez les contacter pour plus de détails.',
    'invalid_email_or_password'         => 'Combinaison invalide ! Veuillez réessayer.',
    'failed_to_create_token'            => 'La création du jeton a échoué',
    'login_success'                     => 'Vous vous êtes connecté avec succès',
    'success' => 'Vous vous êtes connecté avec succès',
    'message_activated_successfully' => 'Votre compte est activé et prêt à être utilisé !',
    'message_verified_successfully' => 'Votre compte a été créé avec succès.',
    'get_profile_success'               => 'Vous avez obtenu votre profil avec succès',
    'token_is_invalid'                  => 'Le jeton est invalide',
    'token_is_expired'                  => 'Le lien que vous utilisez n\'est plus valide. Veuillez réessayer.',
    'something_is_wrong'                => 'Quelque chose ne va pas',
    'language_not_supported'            => 'Langue non prise en charge',
    'invalid_current_password'          => 'Le mot de passe actuel est invalide',
    'changed_password_successfully'     => 'Vous avez changé votre mot de passe avec succès',
    'forgot_password_send_link_success' => 'Nous vous avons envoyé par e-mail votre lien de réinitialisation de mot de passe !',
    'forgot_password_send_link_failed'  => 'Nous ne pouvons pas envoyer l\'e-mail. Veuillez réessayer !',
    'changed_status_successfully'       => 'Vous avez changé le statut avec succès',
    'invalid_permission'                => 'Votre compte n\'a pas cette permission',

    'message_email_address_not_recognised' => 'Cette adresse e-mail n\'a pas été reconnue.',
    'message_register_successfully' => 'Vérifiez votre boîte de réception ou vos spams et suivez le lien pour activer votre compte.',
    'login' => 'Se connecter',
    'forgot_password' => 'Mot de passe oublié ?',
    'email' => 'Adresse e-mail',
    'password' => 'Mot de passe',
    'remember' => 'Se souvenir de moi',
    'back_to_login' => 'Retour à la connexion',
    'restore_password' => 'Réinitialiser le mot de passe',
    'forgot_password_subject' => 'Réinitialisez votre mot de passe It\'s Ready Manager',

    /* Descriptions */
    'login_description' => '<p class="font-18">Connectez-vous avec votre compte <a class="text-underline" href="https://itsready.be/" target="_blank">It\'s Ready</a> et commencez votre commande.</p>',
    'login_description_group' => '<p class="font-18" style="max-width:620px">Connectez-vous avec votre compte <a class="text-underline" href="https://itsready.be/" target="_blank">It\'s Ready</a> et passez une commande groupée. Votre <b>entreprise</b>, <b>groupe</b> ou <b>classe</b> n\'est pas encore enregistré(e) ? <a href=":url">Contactez-nous</a>.</p>',

    /* Buttons */
    'button_login' => 'Se connecter',
    'button_register' => 'S\'inscrire',
    'fill_in_all_fields'                => 'Veuillez remplir tous les champs.',

    'validation' => [
        'email_required' => 'L\'e-mail est invalide',
        'password_required' => 'Le mot de passe est requis',
        'current_password_required' => 'Le mot de passe actuel est requis',
        'new_password_required' => 'Le nouveau mot de passe est requis',
        'password_confirmation_required' => 'La confirmation du mot de passe est requise',
    ],

    'login_modal' => [
        'title' => 'Envie de quelque chose de bon ? Un peu de patience...',
        'description' => 'Connectez-vous avec votre compte <a href="https://itsready.be/" target="_blank" style="text-decoration: underline; font-size: 18px;">It\'s Ready</a> ou créez un nouveau compte.',
        'button_register' => 'Pas encore de compte ? Inscrivez-vous maintenant',
    ],

    'register_modal' => [
        'title' => 'Envie de quelque chose de bon ? Un peu de patience...',
        'description' => 'Remplissez vos informations pour créer un compte <a href="https://itsready.be/" target="_blank" style="text-decoration: underline; font-size: 18px;">It\'s Ready</a>.',
        'button_register' => 'S\'inscrire',
        'button_back' => '< Retour',
        'confirmation_title' => 'E-mail envoyé',
        'confirmation_description' => 'Vérifiez votre boîte de réception ou vos spams et suivez le lien pour activer votre compte.',
    ],

    'forgot_password_modal' => [
        'title' => 'Envie de quelque chose de bon ? Un peu de patience...',
        'description' => 'Saisissez votre adresse e-mail pour réinitialiser votre mot de passe.',
        'button_back' => '< Retour',
        'button_back_naar' => '< Retour à la connexion',
        'confirmation_title' => 'E-mail envoyé',
        'password_changed' => 'Mot de passe modifié',
        'button_back_naar_itsready' => 'Retour à itsready.be',
        'confirmation_description' => 'Vérifiez votre boîte de réception ou vos spams et réinitialisez votre mot de passe.',
    ],
    'ready' => 'Prêt !',
    'of' => 'Ou',
    'login_with' => 'Se connecter avec',
    'register_with' => 'S\'inscrire avec',
    'email_short' => 'E-mail',
    'login_v2' => 'Se connecter',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
