@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('workspace.title')
                    </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            @include($guard.'.workspaces.partials.quick_search')
                        </li>
                        @if(Helper::checkUserPermission($guard.'.workspace@assignaccountmanager'))
                            <li>
                                <a id="assign-manager" href="javascript:;" class="ir-btn ir-btn-primary mgr-20">
                                    @lang('workspace.account_manager')
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="#" class="ir-btn ir-btn-primary" data-toggle="modal" data-target="#form-create">
                                <i class="ir-plus"></i> @lang('workspace.add_workspace')
                            </a>
                        </li>
                        
                        @include($guard.'.workspaces.partials.modal_create')
                        @include($guard.'.workspaces.partials.modal_assign_manager')
                    </ul>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content">
                    @include($guard.'.workspaces.partials.table')
                </div>
            </div>
        </div>
    </div>
@endsection