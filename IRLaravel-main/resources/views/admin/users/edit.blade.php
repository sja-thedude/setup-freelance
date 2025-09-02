@extends('layouts.admin')

@section('content')
    @php($formName = 'userForm')
    <div class="row">
        <div class="x_panel">
            <div class="x_title">
                <h2>@lang('user.edit_user')</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-md-12">
                        @include('ContentManager::partials.errormessage')
                    </div>

                    {!! Form::model($model, ['route' => ['admin.users.update', $model->id], 'method' => 'patch', 'files' => true, 'name' => $formName]) !!}
                    @include('admin.users.partials.form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('ContentManager::partials.scriptdelete')
    @include('ContentManager::user.partials.scriptform')
    @include('admin.users.partials.detect_timezone', ['fromName' => $formName])
@endpush