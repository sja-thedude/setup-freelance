<?php

$originalTranslations = [
    'user'          => [
        'subject_create_admin'         => 'It’s Ready nieuw Admin-account gemaakt',
        'subject_account_verification' => 'Activeer uw It’s Ready-account',
        'subject_confirm_change_email' => 'Bevestig het verzoek om het e-mailadres te wijzigen',
        'subject_reset_password'       => 'Herstel uw It’s Ready-wachtwoord',
    ],
    'contact'       => [
        'subject_new_contact' => 'Nieuwe groepsbestelling-aanvraag via It’s Ready',
        'subject_contact_to_admin' => 'Bericht van het contactformulier',
    ],
    'job'           => [
        'subject_new_job' => 'Reactie op uw vacature via uw app',
    ],
    'reward'        => [
        'subject_physical_gift' => ':title succesvol ingewisseld!',
    ],
    'reminder'      => [
        'subject'      => 'Uw bestelling is bijna klaar!',
        'content1'     => "E-mail onderwerp",
        'content2'     => "Dag :first_name",
        'content3'     => "Uw bestelling <b>:order_id</b> bij <b>:restaurant</b> zal klaar staan voor <b>afhaal</b> om <b>:time</b>",
        'content4'     => "Bedankt voor uw bestelling en eet smakelijk!",
        'content5'     => "Besteloverzicht",
        'content12'    => "Uw bestelling <b>:order_id</b> bij <b>:restaurant</b> zal <b>geleverd</b> worden om :time.",
        'content_note' => 'Opgelet: dit is een testbestelling',
    ],
    'order_success' => [
        'subject'      => 'Bedankt voor uw bestelling :code!',
        'content1'     => "E-mail onderwerp",
        'content2'     => "[0,1]Dag :first_name|{2}Beste|{3}Dag :first_name|[4,*]Beste",
        'content3'     => "Bedankt voor uw bestelling bij <b>:restaurant</b>. Hierbij ontvangt u uw orderbevestiging voor bestelling <b>:order_id</b>. U kunt uw bestelling komen <b>:type</b> op <b>:date</b> om <b>:time</b>.",
        'content4'     => "Bedankt voor uw bestelling en eet smakelijk!",
        'content5'     => "Besteloverzicht",
        'content12'    => "Bedankt voor uw bestelling! Hierbij ontvangt u uw orderbevestiging voor bestelling <b>:order_id</b>. Uw bestelling bij <b>:restaurant</b> wordt <b>geleverd</b> op <b>:date</b> om <b>:time</b>.",
        'content_note' => 'Opgelet: dit is een testbestelling',
        'short_description' => "Bedankt voor uw bestelling bij <b>:restaurant</b>. Hierbij ontvangt u uw orderbevestiging voor bestelling <b>:order_id</b>.",
    ],
    'print_job_monitor' => [
        'subject' => 'Probleem met de printer gedetecteerd!'
    ],
    'hendrickx_kassas_failed' => [
        'subject_fatal' => 'Fatale fout: Probleem met het doorsturen van de bestelling naar Hendrickx Kassas!',
        'subject_notice' => 'Kennisgeving: Probleem met het doorsturen van de bestelling naar Hendrickx Kassas!',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);