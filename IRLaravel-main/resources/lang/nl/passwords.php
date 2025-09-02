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

    'password' => 'Wachtwoorden moeten minimaal zes tekens lang zijn en overeenkomen met de bevestiging.',
    'reset'    => 'Uw nieuw wachtwoord is succesvol ingesteld.',
    'reset_description' => 'Vul uw nieuw wachtwoord in.',
    'sent'     => 'Controleer uw inbox of spam mailbox en volg de link om uw wachtwoord te herstellen.',
    'token'    => 'De link die u gebruikt heeft is niet meer geldig. Gelieve opnieuw te proberen.',
    'user'     => "Uw e-mailadres werd niet herkend. Controleer uw e-mailadres en probeer opnieuw.",
    'forgot_when_did_reset' => 'Neem contact op met de beheerder van het platform om uw wachtwoord opnieuw in te stellen.',
    'description' => '<p class="font-18">Vul uw e-mailadres in om uw wachtwoord te herstellen.</p>',
    'button_reset_password' => 'WACHTWOORD HERSTELLEN',
    'set_new_passwords' => 'Nieuw wachtwoord instellen',
    'set_password' => 'Wachtwoord instellen',

    /* Placeholders */
    'placeholders' => [
        'password' => 'Nieuw wachtwoord',
        'password_confirmation' => 'Nieuw wachtwoord herhalen',
    ],

    'validation' => [
        'password' => [
            'confirmed' => 'Wachtwoorden komen niet overeen.',
            'same' => 'Wachtwoorden komen niet overeen.',
            'min' => 'Gelieve minimum 6 tekens te gebruiken.',
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
