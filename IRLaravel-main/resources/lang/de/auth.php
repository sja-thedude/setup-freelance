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

    'failed'   => 'Ungültige Kombination! Bitte versuchen Sie es erneut.',
    'throttle' => 'Zu viele Anmeldeversuche. Bitte versuchen Sie es in :seconds Sekunden erneut.',
    'banned'   => 'Ihr Konto wurde von den Administratoren deaktiviert. Bitte kontaktieren Sie diese für weitere Details.',
    'invalid_email_or_password'         => 'Ungültige Kombination! Bitte versuchen Sie es erneut.',
    'failed_to_create_token'            => 'Token-Erstellung fehlgeschlagen',
    'login_success'                     => 'Sie haben sich erfolgreich angemeldet',
    'success' => 'Sie haben sich erfolgreich angemeldet',
    'message_activated_successfully' => 'Ihr Konto wurde aktiviert und ist einsatzbereit!',
    'message_verified_successfully' => 'Ihr Konto wurde erfolgreich erstellt.',
    'get_profile_success'               => 'Sie haben das Profil erfolgreich abgerufen',
    'token_is_invalid'                  => 'Token ist ungültig',
    'token_is_expired'                  => 'Der von Ihnen verwendete Link ist nicht mehr gültig. Bitte versuchen Sie es erneut.',
    'something_is_wrong'                => 'Etwas ist schief gelaufen',
    'language_not_supported'            => 'Sprache wird nicht unterstützt',
    'invalid_current_password'          => 'Aktuelles Passwort ist ungültig',
    'changed_password_successfully'     => 'Sie haben das Passwort erfolgreich geändert',
    'forgot_password_send_link_success' => 'Wir haben Ihnen den Link zum Zurücksetzen des Passworts per E-Mail gesendet!',
    'forgot_password_send_link_failed'  => 'Wir können keine E-Mail senden. Bitte versuchen Sie es erneut!',
    'changed_status_successfully'       => 'Sie haben den Status erfolgreich geändert',
    'invalid_permission'                => 'Ihr Konto hat keine Berechtigung dafür',

    'message_email_address_not_recognised' => 'Diese E-Mail-Adresse wurde nicht erkannt.',
    'message_register_successfully' => 'Überprüfen Sie Ihren Posteingang oder Spam-Ordner und folgen Sie dem Link, um Ihr Konto zu aktivieren.',
    'login' => 'Anmelden',
    'forgot_password' => 'Passwort vergessen?',
    'email' => 'E-Mail-Adresse',
    'password' => 'Passwort',
    'remember' => 'Erinnere dich an mich',
    'back_to_login' => 'Zurück zur Anmeldung',
    'restore_password' => 'Passwort wiederherstellen',
    'forgot_password_subject' => 'Setzen Sie Ihr It’s Ready Manager Passwort zurück',

    /* Descriptions */
    'login_description' => '<p class="font-18">Melden Sie sich mit Ihrem <a class="text-underline" href="https://itsready.be/" target="_blank">It’s Ready</a> Konto an und starten Sie Ihre Bestellung.</p>',
    'login_description_group' => '<p class="font-18" style="max-width:620px">Melden Sie sich mit Ihrem <a class="text-underline" href="https://itsready.be/" target="_blank">It’s Ready</a> Konto an und geben Sie eine Gruppenbestellung auf. Ist Ihr <b>Unternehmen</b>, <b>Gruppe</b> oder <b>Klasse</b> noch nicht bei uns registriert? <a href=":url">Kontaktieren Sie uns</a>.</p>',

    /* Buttons */
    'button_login' => 'Anmelden',
    'button_register' => 'Registrieren',
    'fill_in_all_fields'                => 'Bitte füllen Sie alle Felder aus.',

    'validation' => [
        'email_required' => 'E-Mail ist ungültig',
        'password_required' => 'Passwort ist erforderlich',
        'current_password_required' => 'Aktuelles Passwort ist erforderlich',
        'new_password_required' => 'Neues Passwort ist erforderlich',
        'password_confirmation_required' => 'Passwortbestätigung ist erforderlich',
    ],

    'login_modal' => [
        'title' => 'Lust auf etwas Leckeres? Bitte warten...',
        'description' => 'Melden Sie sich mit Ihrem <a href="https://itsready.be/" target="_blank" style="text-decoration: underline; font-size: 18px;">It’s Ready</a> Konto an oder erstellen Sie ein neues Konto.',
        'button_register' => 'Noch kein Konto? Jetzt registrieren',
    ],

    'register_modal' => [
        'title' => 'Lust auf etwas Leckeres? Bitte warten...',
        'description' => 'Geben Sie Ihre Daten ein, um ein <a href="https://itsready.be/" target="_blank" style="text-decoration: underline; font-size: 18px;">It’s Ready</a> Konto zu erstellen.',
        'button_register' => 'Registrieren',
        'button_back' => '< Zurück',
        'confirmation_title' => 'E-Mail wurde gesendet',
        'confirmation_description' => 'Überprüfen Sie Ihren Posteingang oder Spam-Ordner und folgen Sie dem Link, um Ihr Konto zu aktivieren.',
    ],

    'forgot_password_modal' => [
        'title' => 'Lust auf etwas Leckeres? Bitte warten...',
        'description' => 'Geben Sie Ihre E-Mail-Adresse ein, um Ihr Passwort wiederherzustellen.',
        'button_back' => '< Zurück',
        'button_back_naar' => '< Zurück zur Anmeldung',
        'confirmation_title' => 'E-Mail gesendet',
        'password_changed' => 'Passwort geändert',
        'button_back_naar_itsready' => 'Zurück zu itsready.be',
        'confirmation_description' => 'Überprüfen Sie Ihren Posteingang oder Spam-Ordner und setzen Sie Ihr Passwort zurück.',
    ],
    'ready' => 'Bereit!',
    'of' => 'Oder',
    'login_with' => 'Anmelden mit',
    'register_with' => 'Registrieren mit',
    'email_short' => 'E-Mail',
    'login_v2' => 'Anmelden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
