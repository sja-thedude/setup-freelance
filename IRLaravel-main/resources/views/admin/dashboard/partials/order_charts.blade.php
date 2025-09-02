@if(empty($autoloadAjax))
    <div class="row mgt-30">
        <div class="col-sm-6 col-xs-12">
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <div class="pull-left ds-title">
                        #@lang('dashboard.order')
                    </div>
                    <div class="pull-right text-lowercase">
                        @lang('dashboard.total'): <span class="order-total">{!! $totalOrderInTimes !!}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <div id="order-charts" class="ds-charts ds-char ds-char-grey"
                        data-locale-months="{{ json_encode(trans('highcharts.months')) }}"
                        data-locale-weekdays="{{ json_encode(trans('highcharts.weekdays')) }}"
                        data-locale-short-months="{{ json_encode(trans('highcharts.shortMonths')) }}"
                    ></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <div class="pull-left ds-title">
                        €@lang('dashboard.revenue')
                    </div>
                    <div class="pull-right text-lowercase">
                        @lang('dashboard.total'): €<span class="revenue-total">{!! $totalRevenueInTimes !!}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <div id="revenue-charts" class="ds-charts ds-char ds-char-green"
                         data-locale-months="{{ json_encode(trans('highcharts.months')) }}"
                         data-locale-weekdays="{{ json_encode(trans('highcharts.weekdays')) }}"
                         data-locale-short-months="{{ json_encode(trans('highcharts.shortMonths')) }}"
                    ></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mgt-50">
        <div class="col-sm-12 col-xs-12">
            <div class="row">
                <div class="col-sm-12 col-xs-12 ds-title">
                    @lang('dashboard.statistic_overview')
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <div class="ds-overview display-flex mgb-30">
                        <div class="overview-statistic-item">
                            <div class="os-number">
                                {!! $orderActives !!}
                            </div>
                            <div class="os-description">
                                @lang('dashboard.order_active')
                            </div>
                        </div>
                        <div class="overview-statistic-item">
                            <div class="os-number">
                                {!! $restaurantActives !!}
                            </div>
                            <div class="os-description">
                                @lang('dashboard.restaurants')
                            </div>
                        </div>
                        <div class="overview-statistic-item">
                            <div class="os-number">
                                {!! $endUsers !!}
                            </div>
                            <div class="os-description">
                                @lang('dashboard.users')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            var convertOrderData = Statistic.fn.convertOrderData(@json($convertOrders), {!! !empty($inOneDay) ? 0 : 1 !!});

            Statistic.fn.setupCharts('order-charts', '{value}', '#413E38', "@lang('dashboard.order')", convertOrderData.orderChartData, {!! !empty($inOneDay) ? 0 : 1 !!});
            Statistic.fn.setupCharts('revenue-charts', '€{value}', '#B5B268', "@lang('dashboard.revenue')", convertOrderData.revenueChartData, {!! !empty($inOneDay) ? 0 : 1 !!});
        </script>
    @endpush
@else
    @push('scripts')
        <script>
            $(document).ready(function () {
                Dashboard.fn.loadChart.call(this);
            });
        </script>
    @endpush
@endif