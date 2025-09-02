@extends('layouts.manager')

@section('content')
    <div class="row groups">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('user.title')
                    </h2>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="search-and-button">
                    <ul class="nav navbar-right panel_toolbox pull-left mgb-30">
                        <li>
                            @include($guard.'.users.partials.quick_search')
                        </li>
                    </ul>
                </div>
                <div class="ir-content">
                    @include($guard.'.users.partials.table')
                </div>
            </div>
        </div>
    </div>
@endsection