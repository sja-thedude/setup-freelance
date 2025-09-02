<?php

$originalTranslations = [
    'not_found'         => 'Nicht gefunden',
    'success'           => 'Erfolg',
    'fail'              => 'Fehler',
    'access_denied'     => 'Zugriff verweigert',
    /* User domain */
    'user'              => [
        'not_found'                     => 'Benutzer nicht gefunden',
        'created_successfully'          => 'Benutzer wurde erstellt',
        'updated_successfully'          => 'Benutzer wurde aktualisiert',
        'deleted_successfully'          => 'Benutzer wurde gelöscht',
        'change_password_successfully'  => 'Sie haben das Passwort erfolgreich geändert.',
        'updated_profile_successfully'  => 'Sie haben Ihr Profil erfolgreich aktualisiert.',
        'your_account_was_banned'       => 'Sie können sich jetzt nicht anmelden, da Ihr Konto gesperrt wurde. Bitte kontaktieren Sie den Website-Besitzer für weitere Informationen!',
        'invalid_login_type'            => 'Ungültiger Anmeldetyp',
        'verified_account_successfully' => 'Sie haben Ihr Konto erfolgreich verifiziert.',
        'invalid_current_password'      => 'Aktuelles Passwort ist ungültig',
        'changed_password_successfully' => 'Sie haben Ihr Passwort erfolgreich geändert',
        'changed_email_successfully'    => 'E-Mail erfolgreich geändert.',
    ],
    'admin'             => [
        'created_successfully'         => 'Sie haben erfolgreich einen neuen Benutzer erstellt. Das Passwort wurde an seine E-Mail-Adresse gesendet.',
        'updated_successfully'         => 'Benutzer wurde erfolgreich aktualisiert.',
        'deleted_successfully'         => 'Benutzer wurde gelöscht',
        'profile_updated_successfully' => 'Ihr Profil wurde erfolgreich aktualisiert.',
    ],
    /* Role domain */
    'role'              => [
        'not_found'            => 'Rolle nicht gefunden',
        'created_successfully' => 'Rolle wurde erstellt',
        'updated_successfully' => 'Rolle wurde aktualisiert',
        'deleted_successfully' => 'Rolle wurde gelöscht',
    ],
    /* Country domain */
    'country'           => [
        'not_found'            => 'Land nicht gefunden',
        'created_successfully' => 'Land wurde erstellt',
        'updated_successfully' => 'Land wurde aktualisiert',
        'deleted_successfully' => 'Land wurde gelöscht',
    ],
    /* Banner domain */
    'banner'            => [
        'not_found'            => 'Banner nicht gefunden',
        'created_successfully' => 'Banner wurde erstellt',
        'updated_successfully' => 'Banner wurde aktualisiert',
        'deleted_successfully' => 'Banner wurde gelöscht',
    ],
    /* Contact domain */
    'contact'           => [
        'not_found'            => 'Kontakt nicht gefunden',
        'created_successfully' => 'Vielen Dank für Ihr Feedback.',
    ],
    /* Post domain */
    'post'              => [
        'not_found'            => 'Beitrag nicht gefunden',
        'created_successfully' => 'Beitrag wurde erstellt',
        'updated_successfully' => 'Beitrag wurde aktualisiert',
        'deleted_successfully' => 'Beitrag wurde gelöscht',
    ],
    /* Category domain */
    'category'          => [
        'not_found'            => 'Kategorie nicht gefunden',
        'created_successfully' => 'Kategorie wurde erstellt',
        'updated_successfully' => 'Kategorie wurde aktualisiert',
        'deleted_successfully' => 'Kategorie wurde gelöscht',
    ],
    'lang'              => 'Sprache wurde gespeichert',
    'workspace_offline' => 'Dieses Restaurant ist nicht online, daher kann die Bestellung nicht erstellt werden. Bitte versuchen Sie es später erneut!',
    'upload_successfully' => 'Erfolgreich hochgeladen',
    'upload_fail' => 'Hochladen fehlgeschlagen',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
