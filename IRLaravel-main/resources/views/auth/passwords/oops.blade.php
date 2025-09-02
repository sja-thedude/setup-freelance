@extends('layouts.oops')
@section('content')
    <div class="container">
        <h1>{{trans('workspace.oops')}}</h1>
        <p>{{trans('passwords.token')}}</p>

        <a class="btn btn-primary">{{trans('auth.forgot_password_modal.button_back_naar_itsready')}}</a>
    </div>
@endsection