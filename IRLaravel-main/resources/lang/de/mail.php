<?php

$originalTranslations = [
    'user'          => [
        'subject_create_admin'         => 'Priceless-IT neues Admin-Konto erstellt',
        'subject_account_verification' => 'Aktivieren Sie Ihr It\'s Ready-Konto',
        'subject_confirm_change_email' => 'Bestätigen Sie die Anfrage zur Änderung der E-Mail-Adresse',
        'subject_reset_password'       => 'Setzen Sie Ihr It\'s Ready-Passwort zurück',
    ],
    'contact'       => [
        'subject_new_contact' => 'Neue Gruppenbestellungsanfrage über It\'s Ready',
        'subject_contact_to_admin' => 'Nachricht vom Kontaktformular',
    ],
    'job'           => [
        'subject_new_job' => 'Antwort auf Ihre Stellenanzeige über Ihre App',
    ],
    'reward'        => [
        'subject_physical_gift' => ':title erfolgreich eingelöst!',
    ],
    'reminder'      => [
        'subject'      => 'Ihre Bestellung ist fast fertig!',
        'content1'     => "E-Mail-Betreff",
        'content2'     => "Hallo :first_name",
        'content3'     => "Ihre Bestellung <b>:order_id</b> bei <b>:restaurant</b> wird um <b>:time</b> zur <b>Abholung</b> bereitstehen",
        'content4'     => "Vielen Dank für Ihre Bestellung und guten Appetit!",
        'content5'     => "Bestellübersicht",
        'content12'    => "Ihre Bestellung <b>:order_id</b> bei <b>:restaurant</b> wird um :time <b>geliefert</b>.",
        'content_note' => 'Achtung: Dies ist eine Testbestellung',
    ],
    'order_success' => [
        'subject'      => 'Vielen Dank für Ihre Bestellung :code!',
        'content1'     => "E-Mail-Betreff",
        'content2'     => "[0,1]Hallo :first_name|{2}Sehr geehrte|{3}Hallo :first_name|[4,*]Sehr geehrte",
        'content3'     => "Vielen Dank für Ihre Bestellung bei <b>:restaurant</b>. Hiermit erhalten Sie Ihre Bestellbestätigung für die Bestellung <b>:order_id</b>. Sie können Ihre Bestellung <b>:type</b> am <b>:date</b> um <b>:time</b> abholen.",
        'content4'     => "Vielen Dank für Ihre Bestellung und guten Appetit!",
        'content5'     => "Bestellübersicht",
        'content12'    => "Vielen Dank für Ihre Bestellung! Hiermit erhalten Sie Ihre Bestellbestätigung für die Bestellung <b>:order_id</b>. Ihre Bestellung bei <b>:restaurant</b> wird am <b>:date</b> um <b>:time</b> <b>geliefert</b>.",
        'content_note' => 'Achtung: Dies ist eine Testbestellung',
        'short_description' => "Vielen Dank für Ihre Bestellung bei <b>:restaurant</b>. Hiermit erhalten Sie Ihre Bestellbestätigung für die Bestellung <b>:order_id</b>.",
    ],
    'print_job_monitor' => [
        'subject' => 'Problem mit dem Drucker erkannt!'
    ],
    'hendrickx_kassas_failed' => [
        'subject_fatal' => 'Fataler Fehler: Problem beim Senden der Bestellung an Hendrickx Kassas!',
        'subject_notice' => 'Hinweis: Problem beim Senden der Bestellung an Hendrickx Kassas!',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);