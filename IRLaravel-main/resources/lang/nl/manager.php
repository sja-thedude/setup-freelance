<?php

$originalTranslations = [
    'created_successfully' => 'Account Manager is aangemaakt',
    'updated_successfully' => 'Account Manager is bijgewerkt',
    'deleted_successfully' => 'Succesvol verwijderd',
    'deleted_confirm' => 'Deze handelaar is succesvol verwijderd.',
    'not_found' => 'Manager niet gevonden',

    'add_manager' => 'Nieuwe Account Manager',
    'edit_manager' => 'Bewerk Account Manager',
    'detail_manager' => 'Details Account Manager',
    'are_you_sure_delete' => 'Bent u zeker dat u <strong>:name</strong> wilt verwijderen?',

    'title' => 'Account Managers',
    'name' => 'Naam',
    'email' => 'E-mail',
    'gsm' => 'Gsm',
    'status' => 'Status',
    'active_date' => 'Actief sinds',
    'actions' => 'Acties',
    'status_0' => 'Actief',
    'status_1' => 'Uitnodiging vervallen',
    'status_2' => 'Uitnodiging verstuurd',
    'send_invitation' => 'Uitnodiging opnieuw versturen',
    'choose_another_manager' => 'Wijs klanten eerst toe aan andere account manager!',
    'delete_account_manager' => 'Account Manager verwijderen',
    'account_manager' => 'Account Manager',
    'resend_invitation' => 'Uitnodiging opnieuw verzenden',
    'reset_invitation_confirm' => 'Weet u zeker dat u opnieuw een uitnodiging wilt versturen?',
    'send_invitation_subject' => 'Welkom bij It’s Ready',
    'sent_invitation' => 'Uw uitnodiging werd succesvol verstuurd.',
    'sent_invitation_success' => 'Succesvol verstuurd',
    'first_name' => 'Voornaam',
    'last_name' => 'Naam',
    'edit_profile' => 'Profiel wijzigen',
    'change_password' => 'Wachtwoord wijzigen',
    'add_account_manager' => 'Voeg account manager toe',
    'submit_new_account_manager' => 'Uitnodiging versturen',

    'validation' => [
        'name_required' => 'Naam is verplicht',
        'email_required' => 'E-mail is verplicht'
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);