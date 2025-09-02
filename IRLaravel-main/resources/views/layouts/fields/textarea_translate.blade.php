@php
    // When not transfer when include
    if (empty($info)) {
        $info = [];
    }

    // Push name to info
    if (empty($info['name'])) {
        $info['name'] = $name;
    }

    // Base options
    $tmpOptions = [
        'class' => 'form-control',
        'data-info' => json_encode($info),
        'placeholder' => 'for ' . $language,
    ];

    if (!empty($options)) {
        // Merge with new option & overwrite
        $tmpOptions = $options + $tmpOptions;
    }

@endphp
{!! Form::textarea("{$locale}[{$name}]", Helper::translate($model, $locale, $name), $tmpOptions) !!}