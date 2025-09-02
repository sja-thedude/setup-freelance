@extends('layouts.blank')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3 col-xs-12">
                <div class="panel panel-login">
                    <div class="panel-heading">
                        <div class="logo">
                            <i class="fa fa-anchor"></i>
                        </div>
                        <h2>Email changed</h2>
                    </div>
                    <div class="panel-body">
                        <div class="panel-heading text-center">@lang('strings.user.changed_email_successfully')</div>
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 text-center">
                                <a href="{!! route('manager.showlogin') !!}" class="btn btn-default">
                                    @lang('common.back_to_login')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <link rel="stylesheet" href="{{URL::to('/')}}/themes/dashboard/css/style.css">
@endpush
