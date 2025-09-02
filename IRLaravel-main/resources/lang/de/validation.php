<?php

$originalTranslations = [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'Das :attribute muss akzeptiert werden.',
    'active_url'           => 'Das :attribute ist keine gültige URL.',
    'after'                => 'Das :attribute muss ein Datum nach :date sein.',
    'alpha'                => 'Das :attribute darf nur Buchstaben enthalten.',
    'alpha_dash'           => 'Das :attribute darf nur Buchstaben, Zahlen und Bindestriche enthalten.',
    'alpha_num'            => 'Das :attribute darf nur Buchstaben und Zahlen enthalten.',
    'array'                => 'Das :attribute muss ein Array sein.',
    'before'               => 'Das :attribute muss ein Datum vor :date sein.',
    'between'              => [
        'numeric' => 'Das :attribute muss zwischen :min und :max liegen.',
        'file'    => 'Das :attribute muss zwischen :min und :max Kilobytes groß sein.',
        'string'  => 'Das :attribute muss zwischen :min und :max Zeichen lang sein.',
        'array'   => 'Das :attribute muss zwischen :min und :max Elemente haben.',
    ],
    'boolean'              => 'Das :attribute Feld muss wahr oder falsch sein.',
    'confirmed'            => 'Die :attribute Bestätigung stimmt nicht überein.',
    'date'                 => 'Das :attribute ist kein gültiges Datum.',
    'date_format'          => 'Das :attribute entspricht nicht dem Format :format.',
    'different'            => 'Das :attribute und :other müssen unterschiedlich sein.',
    'digits'               => 'Das :attribute muss :digits Ziffern sein.',
    'digits_between'       => 'Das :attribute muss zwischen :min und :max Ziffern sein.',
    'distinct'             => 'Das :attribute Feld hat einen doppelten Wert.',
    'email'                => 'Das :attribute muss eine gültige E-Mail-Adresse sein.',
    'exists'               => 'Das ausgewählte :attribute ist ungültig.',
    'filled'               => 'Das :attribute Feld ist erforderlich.',
    'image'                => 'Das :attribute muss ein Bild sein.',
    'in'                   => 'Das ausgewählte :attribute ist ungültig.',
    'in_array'             => 'Das :attribute Feld existiert nicht in :other.',
    'integer'              => 'Das :attribute muss eine Ganzzahl sein.',
    'ip'                   => 'Das :attribute muss eine gültige IP-Adresse sein.',
    'json'                 => 'Das :attribute muss ein gültiger JSON-String sein.',
    'max'                  => [
        'numeric' => 'Das :attribute darf nicht größer als :max sein.',
        'file'    => 'Das :attribute darf nicht größer als :max Kilobytes sein.',
        'string'  => 'Das :attribute darf nicht mehr als :max Zeichen haben.',
        'array'   => 'Das :attribute darf nicht mehr als :max Elemente haben.',
    ],
    'mimes'                => 'Das :attribute muss eine Datei des Typs: :values sein.',
    'min'                  => [
        'numeric' => 'Das :attribute muss mindestens :min sein.',
        'file'    => 'Das :attribute muss mindestens :min Kilobytes groß sein.',
        'string'  => 'Das :attribute muss mindestens :min Zeichen lang sein.',
        'array'   => 'Das :attribute muss mindestens :min Elemente haben.',
    ],
    'not_in'               => 'Das ausgewählte :attribute ist ungültig.',
    'numeric'              => 'Das :attribute muss eine Zahl sein.',
    'present'              => 'Das :attribute Feld muss vorhanden sein.',
    'regex'                => 'Das :attribute Format ist ungültig.',
    'required'             => 'Das :attribute Feld ist erforderlich.',
    'required_if'          => 'Das :attribute Feld ist erforderlich, wenn :other :value ist.',
    'required_unless'      => 'Das :attribute Feld ist erforderlich, es sei denn, :other ist in :values.',
    'required_with'        => 'Das :attribute Feld ist erforderlich, wenn :values vorhanden ist.',
    'required_with_all'    => 'Das :attribute Feld ist erforderlich, wenn :values vorhanden ist.',
    'required_without'     => 'Das :attribute Feld ist erforderlich, wenn :values nicht vorhanden ist.',
    'required_without_all' => 'Das :attribute Feld ist erforderlich, wenn keiner der :values vorhanden ist.',
    'same'                 => 'Das :attribute und :other müssen übereinstimmen.',
    'size'                 => [
        'numeric' => 'Das :attribute muss :size sein.',
        'file'    => 'Das :attribute muss :size Kilobytes groß sein.',
        'string'  => 'Das :attribute muss :size Zeichen lang sein.',
        'array'   => 'Das :attribute muss :size Elemente enthalten.',
    ],
    'string'               => 'Das :attribute muss ein String sein.',
    'timezone'             => 'Das :attribute muss eine gültige Zone sein.',
    'unique'               => 'Das :attribute ist bereits vergeben.',
    'url'                  => 'Das :attribute Format ist ungültig.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'benutzerdefinierte Nachricht',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'current_password' => 'Passwort',
        'password' => 'Passwort',
        'new_password' => 'neues Passwort',
        'password_confirmation' => 'Passwortbestätigung',
    ],

    'email.unique' => 'Diese E-Mail existiert bereits im System. Bitte versuchen Sie, sich mit einer anderen E-Mail-Adresse zu registrieren oder Ihr Passwort zurückzusetzen.',
    'after_progess_time' => 'Die Endzeit muss später als die Startzeit sein.',
    'phone.min' => 'Das :attribute muss mindestens :min Zeichen lang sein.',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
