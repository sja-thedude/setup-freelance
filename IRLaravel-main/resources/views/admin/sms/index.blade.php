@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <div class="row">
                        <div class="col-md-6">
                            <h2 class="ir-h2">
                                @lang('sms.sms')
                            </h2>
                        </div>
                        <div class="col-md-6">
                            <h4 class="ir-h4">
                                @lang('sms.sms-count') : {{ $sms->total() }}
                            </h4>
                        </div>
                    </div>

                    @include($guard.'.sms.partials.quick_search')
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content">
                    @include($guard.'.sms.partials.table')
                </div>
            </div>
        </div>
    </div>
@endsection