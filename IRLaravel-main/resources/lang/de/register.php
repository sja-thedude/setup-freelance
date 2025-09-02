<?php

$originalTranslations = [

    /* Fields */
    'placeholders' => [
        'first_name' => 'Vorname',
        'last_name' => 'Nachname',
        'gsm' => 'Handynummer',
        'address' => 'Geben Sie hier Ihre Adresse ein',
        'email' => 'E-Mail-Adresse',
        'birthday' => 'Geburtsdatum',
        'password' => 'Passwort',
        'password_confirmation' => 'Passwort bestätigen',
    ],

    /* Descriptions */
    'register_description' => '<p class="font-18">Geben Sie Ihre Daten ein, um ein <a class="font-18" href="https://itsready.be/" target="_blank">It’s Ready</a> Konto zu erstellen.</p>',

    'descriptions' => [
        'gsm' => '<span>z.B.:</span> +32488896655',
        'address' => '<span>z.B.:</span> Rijksweg 100, 1000 Brüssel',
    ],

    'options' => [
        'gender' => [
            'male' => 'MANN',
            'female' => 'FRAU',
        ]
    ],

    /* Terms & Conditions */
    'text_terms_and_conditions' => 'Durch die Erstellung dieses Kontos stimme ich den <a href="https://itsready.be/pdf/Algemene-voorwaarden-eindgebruiker%20FINAL.pdf" target="_blank" class="text-underline">Allgemeinen Geschäftsbedingungen</a> und der <a href="https://itsready.be/pdf/Privacy-policy-FINAL.pdf" target="_blank" class="text-underline">Datenschutzrichtlinie</a> zu.',
    'next_one' => 'Weiter',
    /* Validation messages */
    'validation' => [
        'email' => [
            'unique' => 'Diese E-Mail-Adresse wird bereits verwendet.',
            'email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'required' => 'Diese E-Mail-Adresse wird bereits verwendet.',
        ],
        'address' => [
            'location' => 'Diese Adresse ist ungültig.',
        ],
        'gsm' => [
            'invalid' => 'Mindestens 11 Ziffern einschließlich Ländercode. z.B. +32488896655',
        ],
        'birthday' => [
            'date' => 'Dieses Geburtsdatum ist ungültig.'
        ]
    ],
    'checked_condition_term_policy' => 'Bitte akzeptieren Sie die Allgemeinen Geschäftsbedingungen und die Datenschutzrichtlinie.',
    'field_required' => 'Bitte füllen Sie alle Felder aus.',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
