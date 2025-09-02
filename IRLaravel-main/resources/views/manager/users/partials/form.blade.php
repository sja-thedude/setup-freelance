<div class="col-md-9 col-sm-9 col-xs-12">
    <!-- Name Field -->
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Html::decode(Form::label('first_name', trans('user.label_name') . '<span class="required">*</span>')) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::text('first_name', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <!-- Name Field -->
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Html::decode(Form::label('last_name', trans('user.surname') . '<span class="required">*</span>')) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::text('last_name', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <!-- Birthday Field -->
    @php($fieldName = 'birthday')
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Html::decode(Form::label($fieldName, trans('user.label_birthday'))) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            <div class="input-group">
                @include('layouts.fields.date', [
                    'model' => (!empty($model)) ? $model : new \App\Models\User(),
                    'name' => $fieldName,
                    'info' => ['name' => $fieldName],
                    'options' => [
                        'data-max-date' => Helper::getDateFromFormat(\Carbon\Carbon::now()),
                        'autocomplete' => 'off'
                    ]
                ])
                <div class="input-group-addon">
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Field -->
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Form::label('department', trans('user.department')) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::text('department', null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <!-- Phone Field -->
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Form::label('phone', trans('user.label_phone')) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::text('phone', null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <!-- GSM Field -->
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Html::decode(Form::label('gsm', trans('user.gsm_number'))) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::text('gsm', null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <!-- Email Field -->
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Html::decode(Form::label('email', trans('user.label_email') . '<span class="required">*</span>')) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <!-- Country  Field -->
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Html::decode(Form::label('country_id', trans('client.label_country') . '<span class="required">*</span>')) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::select('country_id', $countries, !empty($model->country_id) ? $model->country_id : null, [
                'class' => 'form-control',
                'placeholder' => trans('default.select'),
                'style' => 'width: 100%;',
                'required',
                'data-postal-code' => !empty($model->postcode) ? $model->postcode : null,
                'data-route' => route($guard.'.settings.getPostalCodes'),
                'data-lang' => trans('client.select_postal_code'),
                'data-select-id' => '#postcode'
            ]) !!}
        </div>
    </div>
    
    <!-- Postalcode and city  Field -->
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Html::decode(Form::label('postcode', trans('client.label_postal_and_city') . '<span class="required">*</span>')) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::select('postcode', [], null, [
                'class' => 'form-control',
                'placeholder' => trans('client.select_postal_code'),
                'style' => 'width: 100%;',
                'required'
            ]) !!}
        </div>
    </div>

    <!-- Timezone Field -->
{{--    @php($timezone = (!empty($model) && !empty($model->timezone)) ? $model->timezone : config('app.timezone'))--}}
{{--    <div class="row form-group">--}}
{{--        <div class="col-sm-2 col-xs-12">--}}
{{--            {!! Form::label('timezone', trans('user.label_timezone')) !!}--}}
{{--        </div>--}}
{{--        <div class="col-sm-10 col-xs-12">--}}
{{--            {!! Form::select('timezone', App\Models\User::timezones(), $timezone, ['class' => 'form-control select2']) !!}--}}
{{--        </div>--}}
{{--    </div>--}}

    <input type="hidden" name="is_admin" value="1" />
</div>

<div class="col-md-3 col-sm-3 col-xs-12 img-fullwidth">
    @include('ContentManager::partials.imageUpload',['dataID'=>'userPhoto','dataValue'=>($model != "" ) ? $model->photo : old('photo'),'dataName'=>'photo'])
</div>

<div class="row col-sm-12 col-xs-12 text-center">
    <hr/>
    <!-- Submit Field -->
    <div class="form-group">
        @if(!empty($isProfile))
            <input type="hidden" name="role_id" value="{!! $model->role_id !!}"/>
            {!! Html::link(route($guard.'.users.profile'), trans('strings.cancel'), ['class' => 'btn btn-default']) !!}
        @else
            {!! Html::link(route($guard.'.users.index'), trans('strings.cancel'), ['class' => 'btn btn-default']) !!}
        @endif

        {!! Form::reset(trans('common.reset'), ['class' => 'btn btn-default']) !!}
        {!! Form::submit(trans('strings.save'), ['class' => 'btn btn-primary']) !!}
    </div>
</div>