<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{!! config('app.name') !!}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="{{URL::to('assets/fontawesome/css/font-awesome.min.css')}}">
    <!-- Styles -->
    <link rel="stylesheet" href="{{URL::to('assets/bootstrap/dist/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{URL::to('assets/css/custom.css')}}">

    @stack('style')
</head>
<body id="app-layout-blank">

    @yield('content')

    <!-- JavaScripts -->
    <script src="{{URL::to('assets/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{URL::to('assets/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    @stack('scripts')
</body>
</html>
