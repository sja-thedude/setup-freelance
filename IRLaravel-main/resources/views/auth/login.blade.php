@extends('layouts.web-default')

@section('slider')
    <div class="hp-slider">
        @include('web.partials.slider')

        <div class="wp-content pl-35" id="wrapSwitchAfhaalLevering">
            <div class="row">
                <div class="col-md-12">

                    @php($classInvalid = $errors->any() ? ' invalid ' : '')
                    {!! Form::open(['route' => 'submit_login', 'name' => 'step-register', 'id' => 'form-login', 'class' => 'step-register' . $classInvalid]) !!}

                        <div class="wrap-step active" data-id="1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="wrap-action">
                                        <a class="backPrev dark-grey font-18" href="{{ url('/') }}">
                                            <i class="icn-arrow-left"></i> @lang('strings.back')
                                        </a>
                                        @if (\request()->has('from') && \request()->get('from') === "group")
                                            <div>@lang('auth.login_description_group', ['url' => route('web.contact.index')])</div>
                                        @else
                                            <div>@lang('auth.login_description')</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-3 max-width-326">
                                    <div class="form-line">
                                        {!! Form::email('email', null, ['class' => 'username required font-18', 'placeholder' => trans('auth.email')]) !!}
                                    </div>
                                    <div class="form-line">
                                        <div class="form-input">
                                            {!! Form::password('password', ['class' => 'password required font-18', 'placeholder' => trans('auth.password')]) !!}
                                            <span class="right show-pass active">
                                                <svg class="svg-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <svg class="svg-icon hidden" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5.378 1.31812L19.318 23.6221M1 12.5001C1 12.5001 5 4.83345 12 4.83345C19 4.83345 23 12.5001 23 12.5001C23 12.5001 19 20.1668 12 20.1668C5 20.1668 1 12.5001 1 12.5001ZM15 12.5001C15 14.0879 13.6569 15.3751 12 15.3751C10.3431 15.3751 9 14.0879 9 12.5001C9 10.9123 10.3431 9.62512 12 9.62512C13.6569 9.62512 15 10.9123 15 12.5001Z" stroke="#BDBDBD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-line mb-25">
                                        <a href="{{ route('password.request') }}" class="dark-grey text-underline display-block mb-10 font-18">@lang('auth.forgot_password')</a>
                                        <div class="error">
                                            @foreach ($errors->all() as $error)
                                                <div class="font-18">{{$error}}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    @include('auth.social_login', ['site' => 'login_with'])
                                </div>
                                <div class="clear-fix"></div>
                                <div class="col-md-12">
                                    <div class="form-line">
                                        <button class="btn btn-disable btn-login" disabled type="submit">@lang('auth.button_login')</button>
                                        @if (\request()->get('from') !== "group")
                                            <a href="{{ route('register') }}" class="btn last btn-register">@lang('auth.button_register')</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
    @include('layouts.partials.mobile-header')
@endsection

@section('content')
    <div id="main-body">
        <div class="mobile-login responsive-modal">
            <div class="row">
                <div class="col-md-6 col-md-push-3">
                    <form action="{{ route('submit_login') }}" method="POST" name="form_login">
                        <div class="wrap-popup-card">
                            <a href="javascript:;" class="close"
                                data-dismiss="popup" data-target="#modalLogin"><i class="icn-close"></i></a>
                            <div class="wp-card">
                                <div class="row modal-error-title">
                                    <h5>@lang('auth.login_modal.title')</h5>
                                </div>
                                <div class="row">
                                    <span class="modal-error-content">@lang('auth.login_modal.description')</span>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="modal-login-container">
                                             <div class="custom-form-input">
                                                 <input type="email" name="email" class="login-username required" placeholder="@lang('auth.email')" required="true">
                                             </div>
                                            <div class="custom-form-input">
                                                <input type="password" name="password" class="login-password required" placeholder="@lang('auth.password')" required="true">
                                                <span class="right show-pass active">
                                                    <svg class="svg-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    <svg class="svg-icon hidden" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M5.378 1.31812L19.318 23.6221M1 12.5001C1 12.5001 5 4.83345 12 4.83345C19 4.83345 23 12.5001 23 12.5001C23 12.5001 19 20.1668 12 20.1668C5 20.1668 1 12.5001 1 12.5001ZM15 12.5001C15 14.0879 13.6569 15.3751 12 15.3751C10.3431 15.3751 9 14.0879 9 12.5001C9 10.9123 10.3431 9.62512 12 9.62512C13.6569 9.62512 15 10.9123 15 12.5001Z" stroke="#BDBDBD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="custom-form-input">
                                                <div class="error" style="margin-bottom: 20px;"></div>
                                            </div>
                                            <div class="custom-form-input">
                                            <button type="submit" class="btn btn-modal btn-login btn-pr-custom">@lang('auth.button_login')</button>
                                                <a href="javascript:void(0);" class="last btn-show-forgot-password-modal"
                                                    data-toggle="popup" data-target="#modalForgotPassword">@lang('auth.forgot_password')</a>
                                            </div>
                                        </div>
                                        <div class="register-container">
                                            <a href="javascript:void(0);" class="btn btn-modal btn-register btn-show-register-modal"
                                                data-toggle="popup" data-target="#modalRegister">@lang('auth.login_modal.button_register')</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(!(!empty($is_delivery) && empty($is_takeout)))
            @include('web.partials.slide-category')
        @endif
    </div>
    
    @include('web.partials.footer')
    <div class="mobile-profile-user" id="userModal"></div>

    <div class="mobile-login responsive-modal" data-withoutcart="{{route('web.cart.storeWithoutLogin')}}" data-workspace-id="{{$webWorkspace->id}}"></div>

    <div class="mobile-forgot-password responsive-modal"></div>

    <div class="mobile-register responsive-modal"></div>

    <div class="mobile-forgot-password-confirmation responsive-modal"></div>

    <div class="mobile-register-confirmation responsive-modal"></div>

    @include('web.partials.user_menu')
    {{-- Include popup login --}}
    @include('web.partials.popup_login')

@endsection