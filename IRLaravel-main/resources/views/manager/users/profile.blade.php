@extends('layouts.manager')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>@lang('user.profile_title')</h2>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="x_content">
                    <div class="row">
                        <form method="POST" action="#" class="form-view">
                            <input type="hidden" id="data-token" value="{{ csrf_token()}}">

                            <div class="col-md-3 col-sm-3 col-xs-12">
                                <!-- Current avatar -->
                                <img class="img-responsive avatar-view" src="{{ $model->photo }}" alt="Avatar" title="Change the avatar">
                            </div>

                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <!-- Name Field -->
                                <div class="row form-group">
                                    <div class="col-sm-2 col-xs-12">
                                        {!! Html::decode(Form::label('first_name', trans('user.name'))) !!}
                                    </div>
                                    <div class="col-sm-10 col-xs-12">
                                        <div class="form-control pdt-0">{{ $model->first_name }}</div>
                                    </div>
                                </div>

                                <!-- Name Field -->
                                <div class="row form-group">
                                    <div class="col-sm-2 col-xs-12">
                                        {!! Html::decode(Form::label('last_name', trans('user.surname'))) !!}
                                    </div>
                                    <div class="col-sm-10 col-xs-12">
                                        <div class="form-control pdt-0">{{ $model->last_name }}</div>
                                    </div>
                                </div>

                                <!-- Email Field -->
                                <div class="row form-group">
                                    <div class="col-sm-2 col-xs-12">
                                        {!! Html::decode(Form::label('email', trans('strings.user.label_email'))) !!}
                                    </div>
                                    <div class="col-sm-10 col-xs-12">
                                        <div class="form-control pdt-0">{{ $model->email }}</div>
                                    </div>
                                </div>

                                <!-- Birthday Field -->
                                <div class="row form-group">
                                    <div class="col-sm-2 col-xs-12">
                                        {!! Form::label('birthday', trans('strings.user.label_birthday')) !!}
                                    </div>
                                    <div class="col-sm-10 col-xs-12">
                                        <div class="form-control pdt-0">{{ Helper::getDateFromFormat($model->birthday) }}</div>
                                    </div>
                                </div>

                                <!-- Gender Field -->
                                <div class="row form-group">
                                    <div class="col-sm-2 col-xs-12">
                                        {!! Form::label('department', trans('user.department')) !!}
                                    </div>
                                    <div class="col-sm-10 col-xs-12">
                                        <div class="form-control pdt-0">{!! $model->department !!}</div>
                                    </div>
                                </div>

                                <!-- Phone Field -->
                                <div class="row form-group">
                                    <div class="col-sm-2 col-xs-12">
                                        {!! Form::label('phone', trans('strings.user.label_phone')) !!}
                                    </div>
                                    <div class="col-sm-10 col-xs-12">
                                        <div class="form-control pdt-0">{{ $model->phone }}</div>
                                    </div>
                                </div>

                                <!-- GSM Field -->
                                <div class="row form-group">
                                    <div class="col-sm-2 col-xs-12">
                                        {!! Form::label('gsm', trans('user.gsm_number')) !!}
                                    </div>
                                    <div class="col-sm-10 col-xs-12">
                                        <div class="form-control pdt-0">{!! $model->gsm !!}</div>
                                    </div>
                                </div>

                                <!-- Postcode Field -->
                                @if(!empty($model->country))
                                <div class="row form-group">
                                    <div class="col-sm-2 col-xs-12">
                                        {!! Form::label('postcode', trans('client.label_country')) !!}
                                    </div>
                                    <div class="col-sm-10 col-xs-12">
                                        <div class="form-control pdt-0">{!! $model->country->name !!}</div>
                                    </div>
                                </div>
                                @endif
                                
                                <!-- Postcode Field -->
                                @if(!empty($model->postalCode))
                                <div class="row form-group">
                                    <div class="col-sm-2 col-xs-12">
                                        {!! Form::label('postcode', trans('user.postcode')) !!}
                                    </div>
                                    <div class="col-sm-10 col-xs-12">
                                        <div class="form-control pdt-0">{!! $model->postalCode->postal_code !!}</div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-2 col-xs-12">
                                        {!! Form::label('postcode', trans('user.city')) !!}
                                    </div>
                                    <div class="col-sm-10 col-xs-12">
                                        <div class="form-control pdt-0">{!! $model->postalCode->city !!}</div>
                                    </div>
                                </div>
                                @endif

                                <!-- Timezone Field -->
{{--                                <div class="row form-group">--}}
{{--                                    <div class="col-sm-2 col-xs-12">--}}
{{--                                        {!! Form::label('timezone', trans('strings.user.label_timezone')) !!}--}}
{{--                                    </div>--}}
{{--                                    <div class="col-sm-10 col-xs-12">--}}
{{--                                        <div class="form-control pdt-0">{{ (array_key_exists($model->timezone, App\Models\User::timezones())) ? App\Models\User::timezones($model->timezone) : '' }}</div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}

                                <!-- Submit Field -->
                                <div class="row form-group">
                                    <hr/>
                                    {!! Html::link(route($guard.'.users.editProfile'), trans('strings.edit'), ['class' => 'btn btn-primary']) !!}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection