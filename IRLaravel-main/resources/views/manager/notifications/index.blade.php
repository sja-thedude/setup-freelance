@extends('layouts.manager')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('notification.title')
                    </h2>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="search-and-button">
                    <ul class="nav navbar-left panel_toolbox mgb-30">
                        <li>
                            @include($guard.'.notifications.partials.quick_search')
                        </li>
                    </ul>
                    <ul class="nav navbar-right panel_toolbox mgb-30">
                        <li>
                            <a href="#" class="ir-btn ir-btn-primary" data-toggle="modal" data-target="#form-create">
                                <i class="ir-plus"></i> @lang('notification.new')
                            </a>
                        </li>
                        
                        @include($guard.'.notifications.partials.modal_create')
                    </ul>
                </div>
                <div class="ir-content">
                    @include($guard.'.notifications.partials.table')
                </div>
            </div>
        </div>
    </div>
@endsection