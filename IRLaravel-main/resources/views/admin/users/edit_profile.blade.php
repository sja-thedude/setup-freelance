@extends('layouts.admin')

@section('content')
    @php($formName = 'userForm')
    <div class="row">
        <div class="x_panel">
            <div class="x_title">
                <h2>@lang('user.change_profile')</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-md-12">
                        @include('ContentManager::partials.errormessage')
                    </div>

                    {!! Form::model($model, ['route' => [$guard.'.users.updateProfile'], 'method' => 'patch', 'files' => true, 'name' => $formName]) !!}
                        @include($guard.'.users.partials.form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('ContentManager::partials.scriptdelete')
    @include('ContentManager::user.partials.scriptform')
    @include($guard.'.users.partials.detect_timezone', ['fromName' => $formName])
@endpush