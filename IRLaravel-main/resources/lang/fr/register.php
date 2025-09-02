<?php

$originalTranslations = [

    /* Fields */
    'placeholders' => [
        'first_name' => 'Prénom',
        'last_name' => 'Nom',
        'gsm' => 'GSM',
        'address' => 'Saisissez votre adresse',
        'email' => 'Adresse e-mail',
        'birthday' => 'Date de naissance',
        'password' => 'Mot de passe',
        'password_confirmation' => 'Confirmer le mot de passe',
    ],

    /* Descriptions */
    'register_description' => '<p class="font-18">Remplissez vos informations pour créer un compte <a class="font-18" href="https://itsready.be/" target="_blank">It\'s Ready</a>.</p>',

    'descriptions' => [
        'gsm' => '<span>ex.:</span> +32488896655',
        'address' => '<span>ex.</span> Rue de la Loi 100, 1000 Bruxelles',
    ],

    'options' => [
        'gender' => [
            'male' => 'HOMME',
            'female' => 'FEMME',
        ]
    ],

    /* Terms & Conditions */
    'text_terms_and_conditions' => 'En créant ce compte, j\'accepte les <a href="https://itsready.be/pdf/Algemene-voorwaarden-eindgebruiker%20FINAL.pdf" target="_blank" class="text-underline">conditions générales</a> et la <a href="https://itsready.be/pdf/Privacy-policy-FINAL.pdf" target="_blank" class="text-underline">politique de confidentialité</a>.',
    'next_one' => 'Suivant',
    /* Validation messages */
    'validation' => [
        'email' => [
            'unique' => 'Cette adresse email est déjà utilisée.',
            'email' => 'Veuillez entrer une adresse email valide.',
            'required' => 'L\'adresse e-mail est requise.',
        ],
        'address' => [
            'location' => 'Cette adresse n\'est pas valide.',
        ],
        'gsm' => [
            'invalid' => 'Minimum 11 chiffres, y compris le code du pays. par exemple +32488896655',
        ],
        'birthday' => [
            'date' => 'Cette date de naissance n\'est pas valide.'
        ]
    ],
    'checked_condition_term_policy' => 'Veuillez accepter les conditions générales et la politique de confidentialité.',
    'field_required' => 'Merci de remplir tous les champs.',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
