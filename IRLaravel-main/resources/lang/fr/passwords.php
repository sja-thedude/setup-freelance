<?php

$originalTranslations = [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'password' => 'Les mots de passe doivent comporter au moins six caractères et correspondre à la confirmation.',
    'reset'    => 'Votre nouveau mot de passe a été défini avec succès.',
    'reset_description' => 'Saisissez votre nouveau mot de passe.',
    'sent'     => 'Vérifiez votre boîte de réception ou vos spams et suivez le lien pour réinitialiser votre mot de passe.',
    'token'    => 'Le lien que vous avez utilisé n\'est plus valide. Veuillez réessayer.',
    'user'     => "Nous ne trouvons pas d'utilisateur avec cette adresse e-mail.",
    'forgot_when_did_reset' => 'Veuillez contacter l\'administrateur de la plateforme pour réinitialiser votre mot de passe.',
    'description' => '<p class="font-18">Saisissez votre adresse e-mail pour réinitialiser votre mot de passe.</p>',
    'button_reset_password' => 'RÉINITIALISER LE MOT DE PASSE',
    'set_new_passwords' => 'Définir un nouveau mot de passe',
    'set_password' => 'Définir le mot de passe',

    /* Placeholders */
    'placeholders' => [
        'password' => 'Nouveau mot de passe',
        'password_confirmation' => 'Confirmer le nouveau mot de passe',
    ],

    'validation' => [
        'password' => [
            'confirmed' => 'Les mots de passe ne correspondent pas.',
            'same' => 'Les mots de passe ne correspondent pas.',
            'min' => 'Veuillez utiliser au moins 6 caractères.',
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
