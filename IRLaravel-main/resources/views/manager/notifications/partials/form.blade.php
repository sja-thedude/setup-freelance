<div class="col-md-9 col-sm-9 col-xs-12">
    @if(empty($isProfile))
        <!-- Status Field -->
        <div class="row form-group">
            <div class="col-sm-2 col-xs-12">
                {!! Html::decode(Form::label('active', trans('user.status') . '<span class="required">*</span>')) !!}
            </div>
            <div class="col-sm-10 col-xs-12">
                {!! Form::select('active', [0 => trans('common.inactive'), 1 => trans('common.active')], !isset($model->active) ? 1 : $model->active, ['class' => 'form-control select2', 'required' => 'required']) !!}
            </div>
        </div>
    @endif

    <!-- Name Field -->
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Html::decode(Form::label('first_name', trans('user.first_name') . '<span class="required">*</span>')) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::text('first_name', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <!-- Name Field -->
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Html::decode(Form::label('last_name', trans('user.last_name') . '<span class="required">*</span>')) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::text('last_name', null, ['class' => 'form-control', 'required' => 'required']) !!}
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

    <!-- Phone Field -->
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Form::label('phone', trans('user.label_phone')) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::text('phone', null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <!-- Gender Field -->
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Form::label('gender', trans('strings.user.label_gender')) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::select('gender', $genders, null, ['class' => 'form-control select2']) !!}
        </div>
    </div>

    <!-- Address Field -->
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Form::label('address', trans('strings.user.label_address')) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::textarea('address', null, ['class' => 'form-control', 'rows' => 2]) !!}
        </div>
    </div>

    <!-- Description Field -->
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Form::label('description', trans('strings.user.label_description')) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2]) !!}
        </div>
    </div>

    <!-- Timezone Field -->
    @php($timezone = (!empty($model) && !empty($model->timezone)) ? $model->timezone : config('app.timezone'))
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Form::label('timezone', trans('strings.user.label_timezone')) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::select('timezone', App\Models\User::timezones(), $timezone, ['class' => 'form-control select2']) !!}
        </div>
    </div>

    <input type="hidden" name="is_admin" value="1"/>

    @if(empty($isProfile))
        <hr/>
        <div class="row form-group">
            <div class="col-sm-2 col-xs-12">
                {!! Html::decode(Form::label(null, trans('strings.workspace.plural_name'))) !!}
            </div>
            <div class="col-sm-10 col-xs-12">
                @include($guard.'.users.partials.form_workspace')
            </div>
        </div>
    @endif
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