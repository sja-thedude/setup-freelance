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
        'class' => 'form-control datepicker',
        'data-info' => json_encode($info),
        'data-date-format' => Helper::getJsDateFormat()
    ];

    if (!empty($options)) {
        // Merge with new option & overwrite
        $tmpOptions = $options + $tmpOptions;
    }

@endphp
{{--<div class="input-group date" data-provide="datepicker">--}}
    {!! Form::hidden($name, null) !!}
    {!! Form::text($name . '_display', Helper::getDateFromFormat($model->getAttribute($name)), $tmpOptions) !!}
    {{--<div class="input-group-addon">
        <span class="glyphicon glyphicon-th"></span>
    </div>
</div>--}}