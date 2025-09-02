<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{!! config('app.name') !!}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="{{URL::to('/')}}/assets/fontawesome/css/font-awesome.min.css">
    <!-- Styles -->
    <link rel="stylesheet" href="{{URL::to('/')}}/assets/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{URL::to('/')}}/assets/css/style.css">

    <style>
        body {font-family: 'arial';padding:0 10px;}
        .fa-btn {margin-right: 6px;}
    </style>
</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top"></nav>

    @yield('content')

    <!-- JavaScripts -->
    <script src="{{URL::to('/')}}/assets/jquery/dist/jquery.min.js"></script>
    <script src="{{URL::to('/')}}/assets/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="{{URL::to('/')}}/assets/js/bootstrap-submenu.min.js"></script>
    <script src="{{URL::to('/')}}/assets/js/main.js"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
</body>
</html>
