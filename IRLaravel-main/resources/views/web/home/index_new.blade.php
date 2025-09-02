@extends('layouts.web-home-new')
@section('content')
    <div class="container location-search">
        @include('web.home.partials.location_search')
    </div>
    <div class="static-contents">
        @include('web.home.partials.static-contents')
    </div>
    <div class="container-fluid download-pane">
        @include('web.home.partials.download-pane')
    </div>
@endsection