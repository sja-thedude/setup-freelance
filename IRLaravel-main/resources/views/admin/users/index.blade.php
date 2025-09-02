@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('user.title')
                    </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            @include($guard.'.users.partials.quick_search')
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content">
                    @include($guard.'.users.partials.table')
                </div>
            </div>
        </div>
    </div>
@endsection