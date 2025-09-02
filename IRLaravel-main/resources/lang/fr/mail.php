<?php

$originalTranslations = [
    'user'          => [
        'subject_create_admin'         => 'Nouveau compte administrateur Priceless-IT créé',
        'subject_account_verification' => 'Activez votre compte It\'s Ready',
        'subject_confirm_change_email' => 'Confirmez la demande de changement d\'adresse e-mail',
        'subject_reset_password'       => 'Réinitialisez votre mot de passe It\'s Ready',
    ],
    'contact'       => [
        'subject_new_contact' => 'Nouvelle demande de commande groupée via It\'s Ready',
        'subject_contact_to_admin' => 'Message du formulaire de contact',
    ],
    'job'           => [
        'subject_new_job' => 'Réponse à votre offre d\'emploi via votre application',
    ],
    'reward'        => [
        'subject_physical_gift' => ':title échangé avec succès !',
    ],
    'reminder'      => [
        'subject'      => 'Votre commande est presque prête !',
        'content1'     => "Objet de l'e-mail",
        'content2'     => "Bonjour :first_name",
        'content3'     => "Votre commande <b>:order_id</b> chez <b>:restaurant</b> sera prête pour <b>la collecte</b> à <b>:time</b>",
        'content4'     => "Merci pour votre commande et bon appétit !",
        'content5'     => "Aperçu de la commande",
        'content12'    => "Votre commande <b>:order_id</b> chez <b>:restaurant</b> sera <b>livrée</b> à :time.",
        'content_note' => 'Attention : ceci est une commande test',
    ],
    'order_success' => [
        'subject'      => 'Merci pour votre commande :code !',
        'content1'     => "Objet de l'e-mail",
        'content2'     => "[0,1]Bonjour :first_name|{2}Cher client|{3}Bonjour :first_name|[4,*]Cher client",
        'content3'     => "Merci pour votre commande chez <b>:restaurant</b>. Voici votre confirmation de commande <b>:order_id</b>. Vous pouvez venir <b>:type</b> votre commande le <b>:date</b> à <b>:time</b>.",
        'content4'     => "Merci pour votre commande et bon appétit !",
        'content5'     => "Aperçu de la commande",
        'content12'    => "Merci pour votre commande ! Voici votre confirmation de commande <b>:order_id</b>. Votre commande chez <b>:restaurant</b> sera <b>livrée</b> le <b>:date</b> à <b>:time</b>.",
        'content_note' => 'Attention : ceci est une commande test',
        'short_description' => "Merci pour votre commande chez <b>:restaurant</b>. Voici votre confirmation de commande <b>:order_id</b>.",
    ],
    'print_job_monitor' => [
        'subject' => 'Problème détecté avec l\'imprimante !'
    ],
    'hendrickx_kassas_failed' => [
        'subject_fatal' => 'Erreur fatale : Problème lors du transfert de la commande vers Hendrickx Kassas !',
        'subject_notice' => 'Avis : Problème lors du transfert de la commande vers Hendrickx Kassas !',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);