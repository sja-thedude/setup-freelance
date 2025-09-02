<!DOCTYPE html><!--[if lt IE 7 ]> <html lang="{{ app()->getLocale() }}" class="ie6"> <![endif]-->
<!--[if IE 7 ]> <html lang="{{ app()->getLocale() }}" class="ie7"> <![endif]-->
<!--[if IE 8 ]> <html lang="{{ app()->getLocale() }}" class="ie8"> <![endif]-->
<!--[if IE 9 ]> <html lang="{{ app()->getLocale() }}" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="{{ app()->getLocale() }}"> <!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ItsReady</title>
<meta name="keywords" content="" >
<meta name="description" content="ItsReady" >
<meta name="author" content="ItsReady" >
<meta name="robots" content="index" >
<meta name="googlebot" content="noarchive" >
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Custom Theme Style -->
    @stack('style-top')
    <link href="{{ URL::to('/builds/css/vendor.web.css'). '?v=' . config('app.version_front.css') }} }}" rel="stylesheet">
    <link href="{{ URL::to('/builds/css/main-core.web.css'). '?v=' . config('app.version_front.css') }} }}" rel="stylesheet">
    <link href="{{ URL::to('/builds/css/main.web.css'). '?v=' . config('app.version_front.css') }}" rel="stylesheet">

    @include('layouts.partials.style_theme_home')

    {{-- Custom Style --}}
    @stack('style')

    <script>
        window.DOMAIN = '{{URL::to('/')}}/';
        var defaultLang = '{!! app()->getLocale() !!}';
    </script>

<!-- Mobile Optimized -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
@php
    $currentRoute = Request::route()->getName();
    $classHeader="";
    $routeHide=['web.contact.index', 'web.contact.show', 'register.confirm', 'password.reset' ,'login'];
    if(in_array($currentRoute, $routeHide)){
        $classHeader = "m-hidden";
    }
@endphp

</head>
<body class="body homepage {!! str_replace('.', '-', $currentRoute) !!}">
    <header id="header" class="{!! $classHeader !!}">
        @section('header')
            <div class="main-header">
                <div class="row">
                    <div class="col-md-6 left-header">
                        @if(!empty($webWorkspace->photo))
                            <a href="{!! route($guard.'.index') !!}" class="logo border-circle width-120">
                                <img src="{!! url($webWorkspace->photo) !!}" alt="Logo"/>
                            </a>
                        @endif
                        <h2>{{!empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->title : null}}</h2>
                        <h6>{{!empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->subtitle : null}}</h6>
                    </div>
                    <div class="col-md-6 text-right right-header">
                        @if(auth()->guest())
                            <a class="btn btn-small pull-right color-theme" href="{{ route('login') }}">
                                @lang('strings.menu_my_account')
                            </a>
                            <div class="pull-right">
                                <nav class="menu pull-right menu-white">
                                    <ul>
                                        {{--@include('web.partials.menu-language', ['class' => '-white'])--}}
                                    </ul>
                                </nav>
                            </div>
                        @else
                            <div class="pull-right">
                                <nav class="menu pull-right menu-white">
                                    <ul>
                                        {{--@include('web.partials.menu-language', ['class' => '-white'])--}}
                                        @include('web.partials.menu-profile')
                                    </ul>
                                </nav>
                            </div>
                        @endif
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        @show
    </header>

    @yield('slider')

    <div id="container" class="cd-main-content">
        @yield('content')
    </div>

    @include('web.partials.holiday', ['webWorkspace' => $webWorkspace])

    @if (!auth()->guest())
        @include('web.user.partials.modal-edit-profile')
    @else
{{--        @include('web.partials.popup_login')--}}
    @endif

    <div id="modal-box-map-view" style="display: none"></div>
    <div id="loader-page"></div>

    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={!! config('maps.api_key') !!}&language={{ app()->getLocale() }}&callback=initializeLocationsGoogleMaps"></script>
    <script src="{{ URL::to('/builds/js/vendor.web.js')  . '?v=' . config('app.version_front.js') }}"></script>
    <script src="{{ URL::to('/builds/js/main.web.js') . '?v=' . config('app.version_front.js') }} }}"></script>
    <script>
        @if(!empty($webWorkspace))
            MainWeb.fn.checkIfTemplateAppInstalled('{{$webWorkspace->template_app_ios}}', '{{$webWorkspace->template_app_android}}')
        @endif
    </script>
    @stack('scripts')
</body>
</html>
