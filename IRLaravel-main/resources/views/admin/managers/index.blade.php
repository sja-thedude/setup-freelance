@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('manager.title')
                    </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        @if(Helper::checkUserPermission($guard.'.managers.create'))
                            <li>
                                <a  data-toggle="modal" data-target="#modal_create_manager"
                                    class="ir-btn ir-btn-primary cursor-pointer">
                                    <i class="ir-plus"></i> @lang('manager.add_account_manager')
                                </a>
                            </li>
                            @include($guard.'.managers.partials.modal_create')
                        @endif
                    </ul>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content">
                    @include($guard.'.managers.partials.table')
                </div>
            </div>
        </div>
    </div>
@endsection