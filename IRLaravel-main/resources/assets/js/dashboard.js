function Dashboard() {
}

Dashboard.fn = {
    init: function () {

    },

    loadChart: function(){
        if($('.dashboard-chart-lazyload').length) {
            $('.dashboard-chart-lazyload').map(function(){
                var dashboardChartInit = $(this);
                var autoload = dashboardChartInit.data('autoload');

                if(autoload == 1) {
                    dashboardChartInit.attr('data-autoload', 0);

                    var url = dashboardChartInit.data('route');
                    var _token = $('meta[name="csrf-token"]').attr('content');
                    var dateRange = dashboardChartInit.find('.date-range-picker input[name="date_range"]').val();
                    var startDateRange = dashboardChartInit.find('.date-range-picker input[name="start_time"]').val();
                    var endDateRange = dashboardChartInit.find('.date-range-picker input[name="end_time"]').val();
                    var timezone = $('.auto-detect-timezone').val();
                    var data = {
                        _token: _token,
                        timezone: timezone,
                        date_range: dateRange,
                        start_time: startDateRange,
                        end_time: endDateRange
                    };

                    var onlyDate = startDateRange == endDateRange ? 0 : 1;
                    $('body').loading('toggle');
                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: data,
                        dataType: 'json',
                    }).success(function (response) {
                        if (response.success == true) {
                            $('.dashboard-order-statistic').empty().append(response.data.view);
                            var convertOrderData = Statistic.fn.convertOrderData(response.data.convertOrders, onlyDate);
                            Statistic.fn.setupCharts('order-charts', '{value}', '#413E38', Lang.get(('dashboard.order')), convertOrderData.orderChartData, onlyDate);
                            Statistic.fn.setupCharts('revenue-charts', '€{value}', '#B5B268', Lang.get('dashboard.revenue'), convertOrderData.revenueChartData, onlyDate);
                        }
                    }).error(function (XMLHttpRequest, textStatus, errorThrown) {
                        console.log(XMLHttpRequest, textStatus, errorThrown);
                    }).always(function () {
                        $('body').loading('toggle');
                    });
                }
            });
        }
    },

    rule: function () {
        $(document).ready(function () {
            Dashboard.fn.init.call(this);
        });
    },
};

Dashboard.fn.rule();