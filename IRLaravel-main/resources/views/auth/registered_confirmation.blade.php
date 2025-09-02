@extends('layouts.web-default')

@php
    $primaryColor = !empty($webWorkspace) && !empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->primary_color : null;
    $errors = count($errors) > 0 ? $errors->getMessages() : [];
    $background = $primaryColor;
@endphp

@section('slider')
    <div id="slider-contact">
        <div class="hp-slider">
            <div class="pc-slider">
                @include('web.partials.slider')
            </div>

            <div class="m-header">

            </div>

            <div class="wp-content pl-35" id="wrapSwitchAfhaalLevering">
                <div class="row">
                    <div class="col-md-12">

                        {!! Form::open(['url' => '#', 'name' => 'step-register', 'id' => 'form-register', 'class' => 'step-register']) !!}

                        <div class="wrap-step active" data-id="1">
                            <div class="row ">
                                <div class="col-md-12 pl-35">
                                    <div class="wrap-action">
                                        <a href="{{ route('login') }}" class="dark-grey">
                                            <i class="icn-arrow-left"></i> @lang('strings.back')
                                        </a>
                                        <div>
                                            <p>@lang('auth.message_activated_successfully')</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row custom-col">
                                <div class="col-md-4 pl-35" style="height: 220px;">

                                </div>
                            </div>
                        </div>

                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
        <div class="mobile-header-contact" style="background: {{$background}}">
            <a class="mobile-header-back-button" href="{!! route($guard.'.index') !!}" href="javascript:;"><i class="icon-chevron-left"></i></a>
            <h2>
                {{$workspace['name']}}
            </h2>
            <div class="wrap-title-map-information">
                <i class="mgl-10 information">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="10" cy="10" r="9.5" stroke="#ffffff"/>
                        <path d="M10.8751 7.244C10.5671 7.244 10.3058 7.13667 10.0911 6.922C9.87644 6.70733 9.76911 6.446 9.76911 6.138C9.76911 5.83 9.87644 5.56867 10.0911 5.354C10.3058 5.13 10.5671 5.018 10.8751 5.018C11.1831 5.018 11.4444 5.13 11.6591 5.354C11.8831 5.56867 11.9951 5.83 11.9951 6.138C11.9951 6.446 11.8831 6.70733 11.6591 6.922C11.4444 7.13667 11.1831 7.244 10.8751 7.244ZM9.92311 15.084C9.47511 15.084 9.11111 14.944 8.83111 14.664C8.56044 14.384 8.42511 13.964 8.42511 13.404C8.42511 13.1707 8.46244 12.8673 8.53711 12.494L9.48911 8H11.5051L10.4971 12.76C10.4598 12.9 10.4411 13.0493 10.4411 13.208C10.4411 13.3947 10.4831 13.53 10.5671 13.614C10.6604 13.6887 10.8098 13.726 11.0151 13.726C11.1831 13.726 11.3324 13.698 11.4631 13.642C11.4258 14.1087 11.2578 14.468 10.9591 14.72C10.6698 14.9627 10.3244 15.084 9.92311 15.084Z" fill="#ffffff"/>
                    </svg>
                </i>
                @include('web.partials.info')
                <div class="overlay-mobile">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="main-body">
        <div>
            <div class="sent-successfully">
                <svg width="114" height="82" viewBox="0 0 114 82" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M109 5L37.5 76.5L5 44" stroke="{!! $background !!}" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p>@lang('auth.message_verified_successfully')</p>

                <a class="btn btn-ready mobile-header-back-button" href="{!! route($guard.'.index') !!}" href="javascript:;">
                    <span>
                        @lang('auth.ready')
                    </span>
                </a>
            </div>
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
        'screen' => 'registered_confirmation',
        'token' => $token,
        'verify_token' => $verify_token,
        'redirect_url' => $redirect_url,
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