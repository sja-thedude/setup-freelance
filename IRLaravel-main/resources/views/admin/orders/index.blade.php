@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('order.title')
                    </h2>
                    @include($guard.'.orders.partials.quick_search')
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content">
                    @include($guard.'.orders.partials.table')
                </div>
            </div>
        </div>
    </div>
@endsection