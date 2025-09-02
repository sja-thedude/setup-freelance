@extends('layouts.manager')

@section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2>@lang('strings.change_password')</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="row">
                <div class="col-sm-12">
                    @include('ContentManager::partials.error_only_flash')
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route($guard.'.password.changePassword') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('current_password') ? ' has-error' : '' }}">
                            <div class="row mg-bottom-10">
                                <div class='col-xs-12'>
                                    <label for="current_password" class="control-label">
                                        @lang('strings.label_current_password')
                                        <span class="required">*</span>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class='col-xs-12'>
                                    <input id="current_password" type="password" class="form-control rounded-0" name="current_password">
                                </div>
                            </div>
                            <div class="row">
                                <div class='col-xs-12'>
                                    @if ($errors->has('current_password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('current_password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('new_password') ? ' has-error' : '' }}">
                            <div class="row mg-bottom-10">
                                <div class='col-xs-12'>
                                    <label for="new_password" class="control-label">
                                        @lang('strings.label_new_password')
                                        <span class="required">*</span>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class='col-xs-12'>
                                    <input id="new_password" type="password" class="form-control rounded-0" name="new_password">
                                </div>
                            </div>
                            <div class="row">
                                <div class='col-xs-12'>
                                    @if ($errors->has('new_password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('new_password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <div class="row mg-bottom-10">
                                <div class='col-xs-12'>
                                    <label for="password_confirmation" class="control-label">
                                        @lang('strings.label_confirm_password')
                                        <span class="required">*</span>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class='col-xs-12'>
                                    <input id="confirm_password" type="password" class="form-control rounded-0" name="password_confirmation">
                                </div>
                            </div>
                            <div class="row">
                                <div class='col-xs-12'>
                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row mg-top-10">
                                <div class='col-xs-12'>
                                    <hr/>
                                    <button type="submit" class="btn btn-primary">@lang('strings.change')</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection