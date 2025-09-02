<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{!! config('app.name') !!}</title>

        @stack('pwa_link')

        <link rel="icon" href="{!! url('favicon.ico') !!}" type="image/x-icon" />
        <link rel="apple-touch-icon" href="{!! url('assets/pwa/icon/pwa_logo_152.png') !!}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="theme-color" content="white"/>
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-title" content="{!! config('app.name') !!}">
        <meta name="msapplication-TileImage" content="{!! url('assets/pwa/icon/pwa_logo_144.png') !!}">
        <meta name="msapplication-TileColor" content="#FFFFFF">

        <!-- Fonts -->
        <link rel="stylesheet" href="{{URL::to('assets/fontawesome/css/font-awesome.min.css')}}">
        <!-- Styles -->
        <link rel="stylesheet" href="{{URL::to('assets/bootstrap/dist/css/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{URL::to('assets/css/custom.css')}}">

        @stack('style')

        <script>
            var defaultLang = '{!! app()->getLocale() !!}';
        </script>
    </head>
    <body id="app-layout-blank" class="ir-theme login-layout">
        @yield('content')

        @stack('pwa_script')

        <!-- JavaScripts -->
        <script src="{{URL::to('assets/jquery/dist/jquery.min.js')}}"></script>
        <script src="{{URL::to('assets/bootstrap/dist/js/bootstrap.min.js')}}"></script>
        <script src="{{URL::to('assets/lang-messages/langs.js')}}"></script>
        <script src="{{URL::to('assets/jquery-validation/dist/jquery.validate.min.js')}}"></script>
        <script src="{{URL::to('assets/jquery-validation/dist/additional-methods.min.js')}}"></script>
        <script src="{{URL::to('assets/js/default.js')}}"></script>

        @stack('scripts')
    </body>
</html>
