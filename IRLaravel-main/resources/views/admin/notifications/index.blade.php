@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('notification.title')
                    </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            @include($guard.'.notifications.partials.quick_search')
                        </li>
                        <li>
                            <a href="#" class="ir-btn ir-btn-primary" data-toggle="modal" data-target="#form-create">
                                <i class="ir-plus"></i> @lang('notification.send_message')
                            </a>
                        </li>
                        
                        @include($guard.'.notifications.partials.modal_create')
                    </ul>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content">
                    @include($guard.'.notifications.partials.table')
                </div>
            </div>
        </div>
    </div>
@endsection