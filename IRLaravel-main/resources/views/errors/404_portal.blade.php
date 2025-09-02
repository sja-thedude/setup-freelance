@extends('layouts.oops')
@section('content')
    <div class="row text-center">
        <h1>{{trans('workspace.oops')}}</h1>
        <p>{{trans('passwords.token')}}</p>
        <a class="btn btn-primary" href="{!! route($guard.'.index') !!}">{{trans('auth.forgot_password_modal.button_back_naar_itsready')}}</a>
    </div>
@endsection
