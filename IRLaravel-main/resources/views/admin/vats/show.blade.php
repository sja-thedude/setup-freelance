@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="x_panel">
        <div class="x_title">
            <h2>View Vat</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    @include('adminlte-templates::common.errors')
                    @include('flash::message')
                </div>
                {!! Form::model($vat, ['route' => ['admin.vats.show', $vat->id],
                    'class' => 'form-horizontal form-label-left']) !!}

                    @include('admin.vats.show_fields')

                    <!-- Action Fields -->
                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                    <a href="{!! route('admin.vats.edit', [$vat->id]) !!}" class="btn btn-primary">Edit</a>
                    <a href="{!! route('admin.vats.index') !!}" class="btn btn-default">Back</a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection