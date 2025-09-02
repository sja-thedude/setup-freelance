<?php

$originalTranslations = [
    'created_successfully' => 'Manager wurde erstellt',
    'updated_successfully' => 'Manager wurde aktualisiert',
    'deleted_successfully' => 'Erfolgreich gelöscht',
    'deleted_confirm' => 'Dieser Account Manager wurde erfolgreich gelöscht.',
    'not_found' => 'Manager nicht gefunden',

    'add_manager' => 'Neuer Account Manager',
    'edit_manager' => 'Account Manager bearbeiten',
    'detail_manager' => 'Account Manager Details',
    'are_you_sure_delete' => 'Sind Sie sicher, dass Sie <strong>:name</strong> löschen möchten?',

    'title' => 'Account Manager',
    'name' => 'Name',
    'email' => 'E-Mail',
    'gsm' => 'Handy',
    'status' => 'Status',
    'active_date' => 'Aktivierungsdatum',
    'actions' => 'Aktionen',
    'status_0' => 'Aktiv',
    'status_1' => 'Einladung abgelaufen',
    'status_2' => 'Einladung gesendet',
    'send_invitation' => 'Einladung senden',
    'choose_another_manager' => 'Zuerst, weisen Sie Kunden einem anderen Account Manager zu!',
    'delete_account_manager' => 'Account Manager löschen',
    'account_manager' => 'Account Manager',
    'resend_invitation' => 'Einladung erneut senden',
    'reset_invitation_confirm' => 'Sind Sie sicher, dass Sie die Einladung erneut senden möchten?',
    'send_invitation_subject' => 'Willkommen bei It’s Ready',
    'sent_invitation' => 'Einladung erfolgreich gesendet.',
    'sent_invitation_success' => 'Erfolgreich gesendet',
    'first_name' => 'Vorname',
    'last_name' => 'Nachname',
    'edit_profile' => 'Profil bearbeiten',
    'change_password' => 'Passwort ändern',
    'add_account_manager' => 'Account Manager hinzufügen',
    'submit_new_account_manager' => 'Einladung senden',

    'validation' => [
        'name_required' => 'Name ist erforderlich',
        'email_required' => 'E-Mail ist ungültig'
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);