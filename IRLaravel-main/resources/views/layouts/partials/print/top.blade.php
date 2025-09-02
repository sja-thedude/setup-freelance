<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>{!! config('app.name') !!}</title>

    @if (config('app.env') == 'local')
        <style id="link-vendor">
            @php include(public_path() . '/builds/css/vendor.admin.css'); @endphp
        </style>
        <style id="link-all">
            @php include(public_path() . '/builds/css/all.css'); @endphp
        </style>
    @else
        <link id="link-vendor" href="{{ URL::to('/builds/css/vendor.admin.css') }}" rel="stylesheet">
        <link id="link-all" href="{{ URL::to('/builds/css/all.css') }}" rel="stylesheet">
    @endif

</head>
    <body class="body-print">
        <div class="container" {!! ($width != 'default') ? 'style="width: '.($width - 20).'px"' : '' !!}>