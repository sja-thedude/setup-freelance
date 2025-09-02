<?php

$originalTranslations = [

    /* Fields */
    'placeholders' => [
        'first_name' => 'Voornaam',
        'last_name' => 'Naam',
        'gsm' => 'GSM',
        'address' => 'Vul hier uw adres in',
        'email' => 'E-mailadres',
        'birthday' => 'Geboortedatum',
        'password' => 'Wachtwoord',
        'password_confirmation' => 'Wachtwoord herhalen',
    ],

    /* Descriptions */
    'register_description' => '<p class="font-18">Vul uw gegevens in om een <a class="font-18" href="https://itsready.be/" target="_blank">It’s Ready</a> account aan te maken.</p>',

    'descriptions' => [
        'gsm' => '<span>vb.:</span> +32488896655',
        'address' => '<span>bv.</span> Rijksweg 100, 1000 Brussel',
    ],

    'options' => [
        'gender' => [
            'male' => 'MAN',
            'female' => 'VROUW',
        ]
    ],

    /* Terms & Conditions */
    'text_terms_and_conditions' => 'Bij het aanmaken van dit account ga ik akkoord met <a href="https://itsready.be/pdf/Algemene-voorwaarden-eindgebruiker%20FINAL.pdf" target="_blank" class="text-underline">algemene voorwaarden</a> en <a href="https://itsready.be/pdf/Privacy-policy-FINAL.pdf" target="_blank" class="text-underline">privacybeleid</a>.',
    'next_one' => 'Volgende',
    /* Validation messages */
    'validation' => [
        'email' => [
            'unique' => 'Dit e-mailadres is reeds in gebruik.',
            'email' => 'Gelieve een geldig e-mailadres in te vullen.',
            'required' => 'Dit e-mailadres is reeds in gebruik.',
        ],
        'address' => [
            'location' => 'Deze adres is niet geldig.',
        ],
        'gsm' => [
            'invalid' => 'Minimum 11 cijfers inclusief landcode. bv. +32488896655',
        ],
        'birthday' => [
            'date' => 'Deze geboortedatum is niet geldig.'
        ]
    ],
    'checked_condition_term_policy' => 'Gelieve akkoord te gaan met de algemene voorwaarden en privacyverklaring.',
    'field_required' => 'Gelieve alle velden in te vullen.',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
