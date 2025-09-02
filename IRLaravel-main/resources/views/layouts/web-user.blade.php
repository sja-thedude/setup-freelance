<!DOCTYPE html><!--[if lt IE 7 ]> <html lang="{{ app()->getLocale() }}" class="ie6"> <![endif]-->
<!--[if IE 7 ]> <html lang="{{ app()->getLocale() }}" class="ie7"> <![endif]-->
<!--[if IE 8 ]> <html lang="{{ app()->getLocale() }}" class="ie8"> <![endif]-->
<!--[if IE 9 ]> <html lang="{{ app()->getLocale() }}" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="{{ app()->getLocale() }}"> <!--<![endif]-->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ItsReady</title>
        <meta name="keywords" content="">
        <meta name="description" content="ItsReady">
        <meta name="author" content="ItsReady">
        <meta name="robots" content="index">
        <meta name="googlebot" content="noarchive">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="bearer-token" content="{{ !empty($token) ? $token : '' }}">

        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

        <!-- Custom Theme Style -->
        @stack('style-top')
        <link href="{{ URL::to('/builds/css/vendor.web.css') . '?v=' . config('app.version_front.css') }} }}" rel="stylesheet">
        <link href="{{ URL::to('/builds/css/main-core.web.css') . '?v=' . config('app.version_front.css') }} }}" rel="stylesheet">
        <link href="{{ URL::to('/builds/css/main.web.css') . '?v=' . config('app.version_front.css') }} }}" rel="stylesheet">

        {{-- Custom Style --}}
        @stack('style')

        @php
            $primaryColor = !empty($webWorkspace) && !empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->primary_color : null;
            $secondColor = !empty($webWorkspace) && !empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->second_color : null;
        @endphp
        @include('layouts.partials.style_theme', ['primaryColor' => $primaryColor, 'secondColor' => $secondColor])

        <script>
            window.DOMAIN = '{{URL::to('/')}}/';
            var defaultLang = '{!! app()->getLocale() !!}';
            var start = Date.now();

            var isReadyState = false;
            if(document.readyState === "complete") {
                // Fully loaded!
                isReadyState = true;
            }
            else if(document.readyState === "interactive") {
                // DOM ready! Images, frames, and other subresources are still downloading.
            }
            else {
                // Loading still in progress.
                // To wait for it to complete, add "DOMContentLoaded" or "load" listeners.
                window.addEventListener("DOMContentLoaded", () => {
                    // DOM ready! Images, frames, and other subresources are still downloading.
                });
                window.addEventListener("load", () => {
                    // Fully loaded!
                    isReadyState = true;
                });
            }
            
            setTimeout(function () {
                if (!isReadyState) {
                    $idElement = document.getElementById("loader-page").style.visibility = "initial";
                }
            }, 50);
        </script>
    </head>

    <body class="body default-page {{str_replace('.', '-', Route::currentRouteName())}}">
        <div class="lds-dual-ring" id="loader-page" style="visibility: hidden;"></div>
        <header id="header">
            <div class="main-header">
                <div class="row">
                    @php
                        $galleries = $workspace['gallery'];
                        $background = $primaryColor;
                        if (count($galleries) > 0 && (Route::currentRouteName() != 'web.user.index')) {
                            $firstGallery = $galleries[0];
                            $fullPath = $firstGallery['full_path'];
                            // $background = 'url("' . $fullPath .'") no-repeat';
                        }
                    @endphp
                    <div class="mobile-header" style="background: {{$background}};">
                        <input type="hidden" class="primary-color" value="{{$primaryColor}}">
                        <input type="hidden" class="gallery-url" value="{{$background}}">
                        <a class="mobile-header-back-button" data-url="{!! route($guard.'.index') !!}" href="javascript:;"><i class="icon-chevron-left"></i></a>
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
                    <div class="col-md-5 web-header">
                        <div class="wrap-title-map-information">
                            <a href="{!! route($guard.'.index') !!}" class="avatar border-circle width-80">
                                <img src="{{ url($workspace['photo'] ? $workspace['photo'] : "images/no-img.png") }}" alt="Logo"/>
                            </a>
                            <div class="information">
                                <span>{{!empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->title : null}}</span>
                                <div class="icon-information mgl-10">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="10" cy="10" r="9.5" stroke="{{$primaryColor}}"/>
                                    <path d="M10.8751 7.244C10.5671 7.244 10.3058 7.13667 10.0911 6.922C9.87644 6.70733 9.76911 6.446 9.76911 6.138C9.76911 5.83 9.87644 5.56867 10.0911 5.354C10.3058 5.13 10.5671 5.018 10.8751 5.018C11.1831 5.018 11.4444 5.13 11.6591 5.354C11.8831 5.56867 11.9951 5.83 11.9951 6.138C11.9951 6.446 11.8831 6.70733 11.6591 6.922C11.4444 7.13667 11.1831 7.244 10.8751 7.244ZM9.92311 15.084C9.47511 15.084 9.11111 14.944 8.83111 14.664C8.56044 14.384 8.42511 13.964 8.42511 13.404C8.42511 13.1707 8.46244 12.8673 8.53711 12.494L9.48911 8H11.5051L10.4971 12.76C10.4598 12.9 10.4411 13.0493 10.4411 13.208C10.4411 13.3947 10.4831 13.53 10.5671 13.614C10.6604 13.6887 10.8098 13.726 11.0151 13.726C11.1831 13.726 11.3324 13.698 11.4631 13.642C11.4258 14.1087 11.2578 14.468 10.9591 14.72C10.6698 14.9627 10.3244 15.084 9.92311 15.084Z" fill="{{$primaryColor}}"/>
                                    </svg>
                                    @include('web.partials.info')
                                </div>
                            </div>

                        </div>
                    </div>
                    @if($webWorkspace->active)
                        <div class="col-md-7 right-header web-header">
                            @if(auth()->guest())
                                <a class="btn btn-small pull-right color-theme btn-show-login-modal" href="javascript:;"
                                    data-toggle="popup" data-target="#modalLogin">
                                    @lang('strings.menu_my_account')
                                </a>
                            @else
                                <nav class="menu float-right">
                                    @include('layouts.partials.sub-order-history')
                                </nav>
                            @endif
                        </div>
                    @endif
                    <div class="clearfix"></div>
                </div>
            </div>
        </header>

        @yield('content')

        <div class="mobile-profile-user" id="userModal"></div>

        <div class="mobile-login responsive-modal" data-withoutcart="{{route('web.cart.storeWithoutLogin')}}" data-workspace-id="{{$webWorkspace->id}}"></div>

        <div class="mobile-forgot-password responsive-modal"></div>

        <div class="mobile-register responsive-modal"></div>

        <div class="mobile-forgot-password-confirmation responsive-modal"></div>

        <div class="mobile-register-confirmation responsive-modal"></div>

        <div class="mobile-order-history">
            @include('layouts.partials.mobile-order-history')
        </div>

        @include('web.partials.user_menu')
        
        @include('web.partials.popup_login')

        @include('web.partials.bottom_menu')

        <!--Notification Detail-->
        <div class="pop-up hidden" id="pop-up-eye"></div>

        <!--Order Detail-->
        <div class="pop-up hidden" id="pop-order-detail"></div>

        <!-- Inactive group avoid re-order popup -->
        <div class="pop-up hidden" id="pop-avoid-reorder"></div>

        <div id="mobile-messages-user" class="mobile-messages-user hidden">
            <h4>@lang('frontend.messages')</h4>
            <ul></ul>
        </div>

        @if (!auth()->guest())
            @include('web.user.partials.modal-edit-profile')
        @endif
        
        @include('web.user.partials.popup-holiday')

        <div id="modal-box-map-view" style="display: none"></div>

        <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={!! config('maps.api_key') !!}&language={{ app()->getLocale() }}&callback=initializeLocationsGoogleMaps"></script>
        <script src="{{ URL::to('/builds/js/vendor.web.js')  . '?v=' . config('app.version_front.js') }}"></script>
        <script src="{{ URL::to('/builds/js/main.web.js')  . '?v=' . config('app.version_front.js') }}"></script>

        <script src="{{ URL::to('/js/socket.io.js') }}"></script>
        <script>
            $(document).ready(function() {
                var socket = io.connect('{{config('socket.host')}}');

                socket.on('connect', function () {
                    socket.on('handle_notification', function (data) {
                        $(".number.user-"+data.userId).text(data.count);
                    });
                });
            });
            
        </script>
        @stack('scripts')
    </body>
</html>
