@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="x_panel">
            <div class="x_title">
                <h2>
                    @if(!empty($platform))
                        @lang('role.client_role_title')
                    @else
                        @lang('role.backoffice_role_title')
                    @endif
                </h2>
                <ul class="nav navbar-right panel_toolbox">
                    @if(Helper::checkUserPermission('admin.roles.create'))
                        <li>
                            <a href="{!! route('admin.roles.create', ['platform' => $platform]) !!}"
                               class="btn-toolbox success">
                                <i class="fa fa-plus"></i> @lang('role.add_role')
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="clearfix"></div>
                @include('ContentManager::partials.errormessage')
            </div>
            <div class="x_content">
                @include('admin.roles.table')

                <ul class="nav navbar-right panel_toolbox">
                    @if(Helper::checkUserPermission('admin.roles.create'))
                        <li>
                            <a href="{!! route('admin.roles.create', ['platform' => $platform]) !!}"
                               class="btn-toolbox success">
                                <i class="fa fa-plus"></i> @lang('role.add_role')
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endsection