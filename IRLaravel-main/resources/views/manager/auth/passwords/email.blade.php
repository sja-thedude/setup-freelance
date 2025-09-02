@extends('layouts.login')

<!-- Main Content -->
@section('content')
<div class="container login-page forgot-validation">
    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-xs-12">
            <div class="panel panel-login">
                <div class="panel-heading">
                    <div class="logo">
                        <a href="{!! route('manager.showlogin') !!}">
                            <img src="{!! url('assets/images/logo/logo.svg') !!}"/>
                        </a>
                    </div>
                    <h3 class="ir-h3">@lang('auth.forgot_password')</h3>
                </div>
                <div class="panel-body">
                    @if (session('status'))
                        <form class="form-horizontal">
                            <div class="form-group mgb-0 text-center">
                                {{ session('status') }}
                            </div>
                        </form>
                    @else
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('manager.password.email') }}">
                            {{ csrf_field() }}

                            <div class="form-group ip-has-icon{{ $errors->has('email') ? ' has-error' : '' }}">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.9333 17.4001C16.6593 17.5877 16.3318 17.6813 16 17.6668C15.6682 17.6813 15.3407 17.5877 15.0667 17.4001L8 13.6001V20.3334C8 21.438 8.89543 22.3334 10 22.3334H22C23.1046 22.3334 24 21.438 24 20.3334V13.6001L16.9333 17.4001Z" fill="#8898AA"/>
                                    <path d="M22 9H10C8.89544 9 8.00001 9.89543 8.00001 11V11.6667C7.99871 11.9112 8.12507 12.1386 8.33334 12.2667L15.6667 16.2667C15.769 16.3206 15.8848 16.3437 16 16.3333C16.1152 16.3437 16.231 16.3206 16.3333 16.2667L23.6667 12.2667C23.8749 12.1386 24.0013 11.9112 24 11.6667V11C24 9.89543 23.1046 9 22 9Z" fill="#8898AA"/>
                                </svg>
                                <input id="email" type="email" class="form-control ir-login-input" name="email" value="{{ old('email') }}"
                                       placeholder="@lang('auth.email')">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                    {{ $errors->first('email') }}
                                </span>
                                @endif
                            </div>

                            <div class="form-group mgb-0 text-center">
                                <button type="submit" class="ir-btn-secondary">
                                    @lang('auth.restore_password')
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
                <div class="panel-footer text-center">
                    <a class="btn btn-link none-pdmg-right" href="{{ route('manager.showlogin') }}">
                        @lang('auth.back_to_login')
                    </a>
                    <div class="copyright text-uppercase">
                        @lang('common.copyright')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('layouts.partials.pwa.manager')

@push('style')
    <link rel="stylesheet" href="{{URL::to('/')}}/themes/dashboard/css/style.css">
@endpush
