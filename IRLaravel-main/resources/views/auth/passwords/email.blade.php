@extends('layouts.web-default')

@section('slider')
    <div class="hp-slider">
        @include('web.partials.slider')

        <div class="wp-content pl-35" id="wrapSwitchAfhaalLevering">
            <div class="row">
                <div class="col-md-12">

                    @php($isConfirmation = \Session::has('status'))
                    @php($classInvalid = $errors->any() ? ' invalid ' : '')
                    {!! Form::open(['route' => 'password.email', 'name' => 'step-register', 'id' => 'form-login', 'class' => 'step-register' . $classInvalid]) !!}

                        <div class="wrap-step active" data-id="1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="wrap-action">
                                        <a href="{{ route('login') }}" class="dark-grey font-18">
                                            <i class="icn-arrow-left"></i> @lang('strings.back')
                                        </a>
                                        <div>
                                            @if($isConfirmation)
                                                <p>{!! \Session::get('status') !!}</p>
                                            @else
                                                @lang('passwords.description')
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-line" style="@if($isConfirmation) visibility: hidden; @endif">
                                        {!! Form::email('email', null, ['class' => 'username required', 'placeholder' => trans('auth.email')]) !!}
                                        @if($errors->has('email'))
                                            <span class="error">{{ $errors->first('email') }}</span>
                                        @endif

                                        {{-- Fake field --}}
                                        {!! Form::hidden('password', 'password', ['class' => 'password']) !!}
                                    </div>
                                    <div class="form-line" style="@if($isConfirmation) visibility: hidden; @endif">
                                        <button class="btn btn-disable btn-login btn-line-hight-19" disabled>@lang('passwords.button_reset_password')</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="main-body">
        @if(!(!empty($is_delivery) && empty($is_takeout)))
            @include('web.partials.slide-category')
        @endif
    </div>
    
    @include('web.partials.footer')
@endsection
