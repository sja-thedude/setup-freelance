@extends('layouts.login')

@section('content')
    <div class="container login-page login-validation">
        <div class="row">
            <div class="col-md-4 col-md-offset-4 col-xs-12">
                <div class="panel panel-login">
                    <div class="panel-heading">
                        <div class="logo">
                            <a href="{!! route('admin.showlogin') !!}">
                                <img src="{!! url('assets/images/logo/logo.svg') !!}"/>
                            </a>
                        </div>
                        <h3 class="ir-h3">@lang('auth.login')</h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ Admin::route('showlogin') }}">
                            {{ csrf_field() }}

                            <div class="form-group ip-has-icon{{ $errors->has('email') || $errors->has('common') ? ' has-error' : '' }}">
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

                            <div class="form-group ip-has-icon{{ $errors->has('password') || $errors->has('common') ? ' has-error' : '' }}">
                                <svg width="32" height="32" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.625 9.40269C9.47169 8.91106 10.4521 8.625 11.5 8.625C14.6704 8.625 17.25 11.2046 17.25 14.375C17.25 17.5454 14.6704 20.125 11.5 20.125C8.32959 20.125 5.75 17.5454 5.75 14.375C5.75 12.9224 6.29553 11.5977 7.1875 10.5843V7.1875C7.1875 4.80916 9.12166 2.875 11.5 2.875C13.8783 2.875 15.8125 4.80916 15.8125 7.1875H14.375C14.375 5.60194 13.0856 4.3125 11.5 4.3125C9.91444 4.3125 8.625 5.60194 8.625 7.1875V9.40269ZM12.2188 17.25V15.6803C13.0532 15.3827 13.6562 14.5921 13.6562 13.6562C13.6562 12.4674 12.6888 11.5 11.5 11.5C10.3112 11.5 9.34375 12.4674 9.34375 13.6562C9.34375 14.5921 9.94678 15.3827 10.7812 15.6803V17.25H12.2188Z" fill="#8898AA"/>
                                </svg>
                                <div class="form-input">
                                    <input id="password" type="password" class="form-control ir-login-input" name="password" placeholder="@lang('auth.password')">
                                    <span class="right show-pass active">
                                        <svg class="svg-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <svg class="svg-icon hidden" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5.378 1.31812L19.318 23.6221M1 12.5001C1 12.5001 5 4.83345 12 4.83345C19 4.83345 23 12.5001 23 12.5001C23 12.5001 19 20.1668 12 20.1668C5 20.1668 1 12.5001 1 12.5001ZM15 12.5001C15 14.0879 13.6569 15.3751 12 15.3751C10.3431 15.3751 9 14.0879 9 12.5001C9 10.9123 10.3431 9.62512 12 9.62512C13.6569 9.62512 15 10.9123 15 12.5001Z" stroke="#BDBDBD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </div>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        {{ $errors->first('password') }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label class="container-cb inline-block">
                                    <input type="checkbox" class="form-control" value="1" name="remember"
                                            {{ old('remember') ? 'checked' : '' }}/>
                                    <span class="checkmark"></span>
                                    <span>@lang('auth.remember')</span>
                                </label>
                            </div>
                            
                            <div class="form-group mgb-0 text-center">
                                <button type="submit" class="ir-btn-secondary">
                                    @lang('auth.login')
                                </button>

                                @if ($errors->has('common'))
                                    <div class="error-text">
                                        {{ $errors->first('common') }}
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="panel-footer text-center">
                        <a class="btn btn-link none-pdmg-right" href="{{ route('admin.password.request') }}">
                            @lang('auth.forgot_password')
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

@include('layouts.partials.pwa.admin')

@push('style')
    <link rel="stylesheet" href="{{URL::to('/')}}/themes/dashboard/css/style.css">
@endpush
