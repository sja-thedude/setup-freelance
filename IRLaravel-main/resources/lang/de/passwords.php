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

    'password' => 'Passwörter müssen mindestens sechs Zeichen lang sein und mit der Bestätigung übereinstimmen.',
    'reset'    => 'Ihr neues Passwort wurde erfolgreich gesetzt.',
    'reset_description' => 'Geben Sie Ihr neues Passwort ein.',
    'sent'     => 'Überprüfen Sie Ihren Posteingang oder Spam-Ordner und folgen Sie dem Link, um Ihr Passwort zurückzusetzen.',
    'token'    => 'Der von Ihnen verwendete Link ist nicht mehr gültig. Bitte versuchen Sie es erneut.',
    'user'     => "Wir können keinen Benutzer mit dieser E-Mail-Adresse finden.",
    'forgot_when_did_reset' => 'Bitte kontaktieren Sie den Administrator der Plattform, um Ihr Passwort zurückzusetzen.',
    'description' => '<p class="font-18">Geben Sie Ihre E-Mail-Adresse ein, um Ihr Passwort zurückzusetzen.</p>',
    'button_reset_password' => 'PASSWORT ZURÜCKSETZEN',
    'set_new_passwords' => 'Neues Passwort festlegen',
    'set_password' => 'Passwort festlegen',

    /* Placeholders */
    'placeholders' => [
        'password' => 'Neues Passwort',
        'password_confirmation' => 'Neues Passwort wiederholen',
    ],

    'validation' => [
        'password' => [
            'confirmed' => 'Passwörter stimmen nicht überein.',
            'same' => 'Passwörter stimmen nicht überein.',
            'min' => 'Bitte verwenden Sie mindestens 6 Zeichen.',
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
