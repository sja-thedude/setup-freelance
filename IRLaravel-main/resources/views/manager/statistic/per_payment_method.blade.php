@extends('layouts.'.$guard)

@section('content')
    <div class="row layout-statistic statistic-lazyload" data-autoload="{!! $autoloadAjax !!}" data-route="{!! request()->fullUrl() !!}">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('statistic.title')
                    </h2>
                    <p class="statistic-sub-title mgb-0">@lang('statistic.per_payment_method')</p>
                </div>
                <div class="main-content">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            @include('manager.statistic.partials.search', [
                                'route' => [$guard.'.statistic.perPaymentMethod'],
                                'dateIp' => true,
                                'searchIp' => false
                            ])
                            @include('manager.statistic.partials.print_actions')
                        </div>
                    </div>
                    <div id="statistic-list" >
                        @if(empty($autoloadAjax))
                            @include('manager.statistic.partials.per_payment_method_table')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection