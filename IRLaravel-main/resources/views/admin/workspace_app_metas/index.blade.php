@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="x_panel">
        <div class="x_title">
            <h2>Workspace App Metas</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a href="{!! route('admin.workspaceAppMetas.create') !!}"
                       class="btn-toolbox success">
                        <i class="fa fa-plus"></i> Add New Workspace App Meta
                    </a>
                </li>
            </ul>
            <div class="clearfix"></div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                @include('flash::message')
            </div>
        </div>
        <div class="x_content" style="overflow-x: auto;">
            @include('admin.workspace_app_metas.table')
        </div>
    </div>
</div>
@endsection