@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="x_panel">
        <div class="x_title">
            <h2>View Setting Print</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    @include('adminlte-templates::common.errors')
                    @include('flash::message')
                </div>
                {!! Form::model($settingPrint, ['route' => ['admin.settingPrints.show', $settingPrint->id],
                    'class' => 'form-horizontal form-label-left']) !!}

                    @include('admin.setting_prints.show_fields')

                    <!-- Action Fields -->
                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                    <a href="{!! route('admin.settingPrints.edit', [$settingPrint->id]) !!}" class="btn btn-primary">Edit</a>
                    <a href="{!! route('admin.settingPrints.index') !!}" class="btn btn-default">Back</a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection