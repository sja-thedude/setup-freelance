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

    'failed'   => 'Ongeldige combinatie! Probeer opnieuw.',
    'throttle' => 'Te veel inlogpogingen. Probeer het over :seconds seconden opnieuw.',
    'banned'   => 'Uw account is gedeactiveerd door de beheerders. Neem contact met hen op voor meer details.',
    'invalid_email_or_password'         => 'Onjuiste combinatie, probeer opnieuw.',
    'failed_to_create_token'            => 'Het aanmaken van een token is mislukt',
    'login_success'                     => 'Je bent succesvol ingelogd',
    'success' => 'Je bent succesvol ingelogd',
    'message_activated_successfully' => 'Uw account is geactiveerd en klaar voor gebruik!',
    'message_verified_successfully' => 'Uw account is succesvol aangemaakt.',
    'get_profile_success'               => 'Je hebt een succesvol profiel',
    'token_is_invalid'                  => 'Token is ongeldig',
    'token_is_expired'                  => 'De link die u gebruikt heeft is niet meer geldig. Gelieve opnieuw te proberen.',
    'something_is_wrong'                => 'Er is iets fout',
    'language_not_supported'            => 'Taal wordt niet ondersteund',
    'invalid_current_password'          => 'Huidig wachtwoord is ongeldig',
    'changed_password_successfully'     => 'Je hebt het wachtwoord succesvol gewijzigd',
    'forgot_password_send_link_success' => 'We hebben je link voor het opnieuw instellen van je wachtwoord gemaild!',
    'forgot_password_send_link_failed'  => 'We kunnen geen e-mail verzenden. Probeer het a.u.b. opnieuw!',
    'changed_status_successfully'       => 'Je bent succesvol van status veranderd',
    'invalid_permission'                => 'Uw account heeft deze toestemming niet',

    'message_email_address_not_recognised' => 'Dit e-mailadres werd niet herkend.',
    'message_register_successfully' => 'Controleer uw inbox of spam mailbox en volg de link om uw account te activeren.',
    'login' => 'Inloggen',
    'forgot_password' => 'Wachtwoord vergeten?',
    'email' => 'E-mailadres',
    'password' => 'Wachtwoord',
    'remember' => 'Onthoud mij',
    'back_to_login' => 'Terug naar aanmelden',
    'restore_password' => 'Wachtwoord herstellen',
    'forgot_password_subject' => 'Herstel uw It’s Ready Manager wachtwoord',

    /* Descriptions */
    'login_description' => '<p class="font-18">Log in met uw <a class="text-underline font-18" href="https://itsready.be/" target="_blank">It’s Ready</a> account en start uw bestelling.</p>',
    'login_description_group' => '<p class="font-18" style="max-width:620px">Log in met jouw <a class="text-underline font-18" href="https://itsready.be/" target="_blank">It’s Ready</a> account en plaats een groepsbestelling. Is uw <b>bedrijf</b>, <b>groep</b> of <b>klas</b> nog niet bij ons geregistreerd? <a href=":url" class="font-18">Contacteer ons</a>.</p>',

    /* Buttons */
    'button_login' => 'Inloggen',
    'button_register' => 'Registreren',
    'fill_in_all_fields'                => 'Gelieve alle velden in te vullen.',

    'validation' => [
        'email_required' => 'Dit e-mailadres werd niet herkend.',
        'password_required' => 'Een wachtwoord is verplicht',
        'current_password_required' => 'Huidig wachtwoord is vereist',
        'new_password_required' => 'Nieuw wachtwoord is vereist',
        'password_confirmation_required' => 'Wachtwoordbevestiging is vereist',
    ],

    'login_modal' => [
        'title' => 'Zin in iets lekkers? Nog even geduld...',
        'description' => 'Log in met uw <a href="https://itsready.be/" target="_blank" style="text-decoration: underline; font-size: 18px;">It’s Ready</a> account of maak een nieuw account aan.',
        'button_register' => 'Nog geen account? Registreer nu',
    ],

    'register_modal' => [
        'title' => 'Zin in iets lekkers? Nog even geduld...',
        'description' => 'Vul uw gegevens in om een <a href="https://itsready.be/" target="_blank" style="text-decoration: underline; font-size: 18px;">It’s Ready</a> account aan te maken.',
        'button_register' => 'Registreren',
        'button_back' => '< Terug',
        'confirmation_title' => 'E-mail is verstuurd',
        'confirmation_description' => 'Controleer uw inbox of spam mailbox en volg de link om uw account te activeren.',
    ],

    'forgot_password_modal' => [
        'title' => 'Zin in iets lekkers? Nog even geduld...',
        'description' => 'Vul uw e-mailadres in om uw wachtwoord te herstellen.',
        'button_back' => '< Terug',
        'button_back_naar' => '< Terug naar aanmelden',
        'confirmation_title' => 'E-mail verstuurd',
        'password_changed' => 'Wachtwoord gewijzigd',
        'button_back_naar_itsready' => 'Terug naar itsready.be',
        'confirmation_description' => 'Controleer uw inbox of spam mailbox en stel uw wachtwoord opnieuw in.',
    ],
    'ready' => 'Klaar!',
    'of' => 'Of',
    'login_with' => 'Inloggen met',
    'register_with' => 'Registreren met',
    'email_short' => 'E-mail',
    'login_v2' => 'Inloggen',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
