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
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">


    <!-- Custom Theme Style -->
    @stack('style-top')
    <link href="{{ URL::to('/builds/css/vendor.web.css') . '?v=' . config('app.version_front.css') }} }}" rel="stylesheet">
    <link href="{{ URL::to('/builds/css/main-core.web.css') . '?v=' . config('app.version_front.css') }} }}" rel="stylesheet">
    <link href="{{ URL::to('/builds/css/main.web.css'). '?v=' . config('app.version_front.css') }} }}" rel="stylesheet">

    <style type="text/css">
        .btn.btn-andere,
        .btn-modal,
        .user-modal .modal-content .form-line .checkbox-sex .wrap-content input:checked,
        .xdsoft_datetimepicker .xdsoft_datepicker .xdsoft_calendar td:hover div,
        .xdsoft_datetimepicker .xdsoft_datepicker .xdsoft_calendar td.xdsoft_current div{
            background: #B5B268
        }
        
        a:hover, a:focus,
        .btn-modal.btn-register{
            color: #B5B268
        }
        
        .btn-modal,
        .btn-modal.btn-register,
        .xdsoft_datetimepicker .xdsoft_datepicker .xdsoft_calendar td:hover div,
        .xdsoft_datetimepicker .xdsoft_datepicker .xdsoft_calendar td.xdsoft_current div{
            border-color: #B5B268
        }
       
    </style>

    {{-- Custom Style --}}
    @stack('style')

    <script>
        window.DOMAIN = '{{URL::to('/')}}/';
        var defaultLang = '{!! app()->getLocale() !!}';
    </script>

    <!-- Mobile Optimized -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

</head>
<body class="body @if(isset($isHome)) homepage @else other @endif web-portal @if(!empty($isInSearchPage))page-search @endif">

@include('layouts.partials.cookie-bar')

@include('web.home.header')

<main class="main-content">
    @yield('content')
</main>

@include('web.partials.holiday', ['webWorkspace' => $webWorkspace])

@include('web.home.footer')

<!--Notification Detail-->
<div class="pop-up hidden" id="pop-up-eye"></div>

<!--Order Detail-->
<div class="pop-up hidden" id="pop-order-detail"></div>

@if (!auth()->guest())
    @include('web.user.partials.modal-edit-profile')
@else
    @include('web.partials.popup_login_portal')
@endif

<div id="modal-box-map-view" style="display: none"></div>

<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={!! config('maps.api_key') !!}&language={{ app()->getLocale() }}"></script>
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