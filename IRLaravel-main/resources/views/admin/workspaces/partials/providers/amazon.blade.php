<!-- API key Field -->
<div class="form-group col-sm-6">
    {!! Form::label('providers[' . $provider . '][api_key]', 'API-key') !!}
    {!! Form::text('providers[' . $provider . '][api_key]', null, ['class' => 'form-control']) !!}
</div>

<!-- Secret key Field -->
<div class="form-group col-sm-6">
    {!! Form::label('providers[' . $provider . '][secret_key]', 'Secret') !!}
    {!! Form::text('providers[' . $provider . '][secret_key]', null, ['class' => 'form-control']) !!}
</div>
