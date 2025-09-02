@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="x_panel">
        <div class="x_title">
            <h2>Workspace Jobs</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a href="{!! route('admin.workspaceJobs.create') !!}"
                       class="btn-toolbox success">
                        <i class="fa fa-plus"></i> Add New Workspace Job
                    </a>
                </li>
            </ul>
            <div class="clearfix"></div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                @include('flash::message')
            </div>
        </div>
        <div class="x_content" style="overflow-x: auto;">
            @include('admin.workspace_jobs.table')
        </div>
    </div>
</div>
@endsection