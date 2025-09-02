@extends('layouts.web-default')

@section('slider')
    <div id="slider-contact">
        <div class="hp-slider">
            @include('web.partials.slider')

            <div class="wp-content pl-35" id="wrapSwitchAfhaalLevering">
                <div class="row">
                    <div class="col-md-12">

                        @php($classInvalid = $errors->any() ? ' invalid ' : '')
                        {!! Form::open(['route' => 'password.submit_reset', 'name' => 'step-register', 'id' => 'form-login', 'class' => 'step-register' . $classInvalid]) !!}

                        <div class="wrap-step active" data-id="1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="wrap-action">
                                        <a href="{{ route('password.request') }}" class="dark-grey">
                                            <i class="icn-arrow-left"></i> @lang('strings.back')
                                        </a>
                                        <div><p>@lang('passwords.reset_description')</p></div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-3">
                                    <div class="form-line">
                                        {!! Form::hidden('token', $token) !!}
                                        {!! Form::hidden('email', $email, ['class' => 'username']) !!}
                                    </div>
                                    <div class="form-line">
                                        <div class="form-input">
                                            {!! Form::password('password', ['class' => 'password required', 'placeholder' => trans('passwords.placeholders.password')]) !!}
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
                                    <div class="form-line">
                                        <div class="form-input">
                                            {!! Form::password('password_confirmation', ['class' => 'password required', 'placeholder' => trans('passwords.placeholders.password_confirmation')]) !!}
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
                                        @if($errors->has('password'))
                                            <span class="error">{{ $errors->first('password') }}</span>
                                        @elseif($errors->has('password_confirmation'))
                                            <span class="error">{{ $errors->first('password_confirmation') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="clear-fix"></div>
                                <div class="col-md-12">
                                    <div class="form-line">
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
        @include('layouts.partials.mobile-header')
    </div>
@endsection

@section('content')
    <div id="main-body">
        <div>
            @php($classInvalid = $errors->any() ? ' invalid ' : '')
            {!! Form::open(['route' => 'password.submit_reset', 'name' => 'step-register', 'id' => 'form-login', 'class' => 'step-register' . $classInvalid]) !!}

            <div class="wrap-step active" data-id="1">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="wrap-action">
                            <div><p>@lang('passwords.reset_description')</p></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-3 col-sm-12">
                        <div class="custom-form-input">
                            {!! Form::hidden('token', $token) !!}
                            {!! Form::hidden('email', $email, ['class' => 'username']) !!}
                        </div>
                        <div class="custom-form-input">
                            <div class="form-input">
                                {!! Form::password('password', ['class' => 'password required', 'placeholder' => trans('passwords.placeholders.password'), 'required']) !!}
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
                        <div class="custom-form-input">
                            <div class="form-input">
                                {!! Form::password('password_confirmation', ['class' => 'password required', 'placeholder' => trans('passwords.placeholders.password_confirmation'), 'required']) !!}
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
                            @if($errors->has('password'))
                                <span class="error">{{ $errors->first('password') }}</span>
                            @elseif($errors->has('password_confirmation'))
                                <span class="error">{{ $errors->first('password_confirmation') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="clear-fix"></div>
                    <div class="col-md-12">
                        <div class="custom-form-input">
                            <button class="btn btn-forgot-password btn-line-hight-19">@lang('passwords.button_reset_password')</button>
                        </div>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}
        </div>
        @if(!(!empty($is_delivery) && empty($is_takeout)))
            @include('web.partials.slide-category')
        @endif
    </div>

    @include('web.partials.footer')
@endsection

@push('scripts')
    <script src="{{ asset('js/common.js') }}"></script>

    <!-- implement javascript on web page that first first tries to open the deep link
        1. if user has app installed, then they would be redirected to open the app to specified screen
        2. if user doesn't have app installed, then their browser wouldn't recognize the URL scheme
        and app wouldn't open since it's not installed. In 1 second (1000 milliseconds) user is redirected
        to download app from app store.
     -->
    @php($data = [
        'screen' => 'reset_password',
        'token' => $token,
        'email' => $email,
    ])
    <script>
        function showContent() {
            document.getElementsByTagName('body')[0].style.display = 'block';
        }

        /**
         * Deep Linking to Your Mobile App from Your Website
         * @link https://tune.docs.branch.io/sdk/deep-linking-to-your-mobile-app-from-your-website/
         */
        window.addEventListener('DOMContentLoaded', (event) => {
            // var confirm = window.confirm('Open in It’s Ready app');
            //
            // if (confirm) {
            var device = getMobileOperatingSystem();
            console.log('device:', device);

            if (device === 'Android') {
                <!-- Deep link URL for existing users with app already installed on their device -->
                window.location = '{{ array_get($config, 'android.deeplink') }}?{!! http_build_query($data) !!}';
                <!-- Download URL (TUNE link) for new users to download the app -->
                {{--setTimeout("window.location = '{{ array_get($config, 'android.download') }}';", 1000);--}}
                setTimeout(function () {
                    showContent();
                }, 1000);
            } else if (device === 'iOS') {
                <!-- Deep link URL for existing users with app already installed on their device -->
                window.location = '{{ array_get($config, 'ios.deeplink') }}?{!! http_build_query($data) !!}';
                <!-- Download URL (TUNE link) for new users to download the app -->
                {{--setTimeout("window.location = '{{ array_get($config, 'ios.download') }}';", 1000);--}}
                setTimeout(function () {
                    showContent();
                }, 1000);
            } else {
                showContent();
            }
            // }

        });
    </script>
@endpush