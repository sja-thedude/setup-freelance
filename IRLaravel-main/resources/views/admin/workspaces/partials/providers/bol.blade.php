<div class="row">
    <!-- Sales number Field -->
    <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
        {!! Form::label('providers[' . $provider . '][sales_number]', trans('workspace.sales_number') . '<span class="required">*</span>',
                ['class' => 'control-label'], false) !!}
        {!! Form::text('providers[' . $provider . '][sales_number]', null, ['class' => 'form-control', /*'required' => 'required'*/]) !!}
    </div>
</div>

<div class="row">
    <!-- API key Field -->
    <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
        {!! Form::label('providers[' . $provider . '][api_key]', trans('workspace.api_key') . '<span class="required">*</span>',
                ['class' => 'control-label'], false) !!}
        {!! Form::text('providers[' . $provider . '][api_key]', null, ['class' => 'form-control', /*'required' => 'required'*/]) !!}
    </div>

    <!-- Secret key Field -->
    <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
        {!! Form::label('providers[' . $provider . '][secret_key]', trans('workspace.secret_key') . '<span class="required">*</span>',
                ['class' => 'control-label'], false) !!}
        {!! Form::text('providers[' . $provider . '][secret_key]', null, ['class' => 'form-control', /*'required' => 'required'*/]) !!}
    </div>
</div>

<div class="row">
    <!-- FTPS user Field -->
    <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
        {!! Form::label('providers[' . $provider . '][ftps_user]', trans('workspace.ftps_user')) !!}
        {!! Form::text('providers[' . $provider . '][ftps_user]', null, ['class' => 'form-control']) !!}
    </div>

    <!-- FTPS password Field -->
    <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
        {!! Form::label('providers[' . $provider . '][ftps_password]', trans('workspace.ftps_password')) !!}
        {!! Form::text('providers[' . $provider . '][ftps_password]', null, ['class' => 'form-control']) !!}
    </div>
</div>