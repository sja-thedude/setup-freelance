<?php

$originalTranslations = [

    /* Fields */
    'placeholders' => [
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'gsm' => 'Mobile Number',
        'address' => 'Enter your address here',
        'email' => 'Email Address',
        'birthday' => 'Date of Birth',
        'password' => 'Password',
        'password_confirmation' => 'Confirm Password',
    ],

    /* Descriptions */
    'register_description' => '<p class="font-18">Enter your details to create an <a class="font-18" href="https://itsready.be/" target="_blank">It’s Ready</a> account.</p>',

    'descriptions' => [
        'gsm' => '<span>e.g.:</span> +32488896655',
        'address' => '<span>e.g.</span> Rijksweg 100, 1000 Brussels',
    ],

    'options' => [
        'gender' => [
            'male' => 'MALE',
            'female' => 'FEMALE',
        ]
    ],

    /* Terms & Conditions */
    'text_terms_and_conditions' => 'By creating this account, I agree to the <a href="https://itsready.be/pdf/Algemene-voorwaarden-eindgebruiker%20FINAL.pdf" target="_blank" class="text-underline">terms and conditions</a> and <a href="https://itsready.be/pdf/Privacy-policy-FINAL.pdf" target="_blank" class="text-underline">privacy policy</a>.',
    'next_one' => 'Next',
    /* Validation messages */
    'validation' => [
        'email' => [
            'unique' => 'This email address is already in use.',
            'email' => 'Please enter a valid email address.',
            'required' => 'Email address is required.',
        ],
        'address' => [
            'location' => 'This address is not valid.',
        ],
        'gsm' => [
            'invalid' => 'Minimum 11 digits including country code. e.g. +32488896655',
        ],
        'birthday' => [
            'date' => 'This date of birth is not valid.'
        ]
    ],
    'checked_condition_term_policy' => 'Please agree to the terms and conditions and privacy statement.',
    'field_required' => 'Please fill in all fields.',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
