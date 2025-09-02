@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="x_panel">
        <div class="x_title">
            <h2>Edit Workspace Job</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    @include('adminlte-templates::common.errors')
                    @include('flash::message')
                </div>
                {!! Form::model($workspaceJob, ['route' => ['admin.workspaceJobs.update', $workspaceJob->id],
                    'method' => 'patch', 'class' => 'form-horizontal form-label-left']) !!}

                    @include('admin.workspace_jobs.fields')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection