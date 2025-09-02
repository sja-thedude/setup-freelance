<?php

$originalTranslations = [
    'not_found'         => 'Niet gevonden',
    'success'           => 'Succes',
    'fail'              => 'Mislukt',
    'access_denied'     => 'Toegang geweigerd',
    /* User domain */
    'user'              => [
        'not_found'                     => 'Gebruiker niet gevonden',
        'created_successfully'          => 'Gebruiker is aangemaakt',
        'updated_successfully'          => 'Gebruiker is bijgewerkt',
        'deleted_successfully'          => 'Gebruiker is verwijderd',
        'change_password_successfully'  => 'U heeft het wachtwoord succesvol gewijzigd.',
        'updated_profile_successfully'  => 'Je hebt je profiel succesvol geupdate.',
        'your_account_was_banned'       => '"U kunt nu niet inloggen omdat uw account is geblokkeerd. Neem contact op met Web Owner voor meer informatie!',
        'invalid_login_type'            => 'Ongeldig inlogtype',
        'verified_account_successfully' => 'U heeft uw account succesvol geverifieerd.',
        'invalid_current_password'      => 'Huidig wachtwoord is ongeldig',
        'changed_password_successfully' => 'Je hebt met succes je wachtwoord veranderd',
        'changed_email_successfully'    => 'E-mail succesvol gewijzigd.',
    ],
    'admin'             => [
        'created_successfully'         => 'U heeft met succes een nieuwe gebruiker aangemaakt. Het wachtwoord is naar zijn e-mailadres gestuurd.',
        'updated_successfully'         => 'De gebruiker is succesvol bijgewerkt.',
        'deleted_successfully'         => 'Gebruiker is verwijderd',
        'profile_updated_successfully' => 'Je profiel is succesvol bijgewerkt.',
    ],
    /* Role domain */
    'role'              => [
        'not_found'            => 'Rol niet gevonden',
        'created_successfully' => 'Rol is gemaakt',
        'updated_successfully' => 'Rol is bijgewerkt',
        'deleted_successfully' => 'Rol is verwijderd',
    ],
    /* Country domain */
    'country'           => [
        'not_found'            => 'Land niet gevonden',
        'created_successfully' => 'Land is gemaakt',
        'updated_successfully' => 'Land is bijgewerkt',
        'deleted_successfully' => 'Land is verwijderd',
    ],
    /* Banner domain */
    'banner'            => [
        'not_found'            => 'Banner niet gevonden',
        'created_successfully' => 'Banner is gemaakt',
        'updated_successfully' => 'Banner is bijgewerkt',
        'deleted_successfully' => 'Banner is verwijderd',
    ],
    /* Contact domain */
    'contact'           => [
        'not_found'            => 'Contact niet gevonden',
        'created_successfully' => 'Bedankt voor het sturen van feedback naar ons.',
    ],
    /* Post domain */
    'post'              => [
        'not_found'            => 'Bericht niet gevonden',
        'created_successfully' => 'Bericht is gemaakt',
        'updated_successfully' => 'Bericht is bijgewerkt',
        'deleted_successfully' => 'Bericht is verwijderd',
    ],
    /* Category domain */
    'category'          => [
        'not_found'            => 'Categorie niet gevonden',
        'created_successfully' => 'Categorie is gemaakt',
        'updated_successfully' => 'Categorie is bijgewerkt',
        'deleted_successfully' => 'Categorie is verwijderd',
    ],
    'lang'              => 'Taal is opgeslagen',
    'workspace_offline' => 'Het order kan niet afgerond worden omdat het restaurant offline is. Probeer het later opnieuw!',
    'upload_successfully' => 'Upload succesvol',
    'upload_fail' => 'Upload mislukt',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
