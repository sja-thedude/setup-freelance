function StatisticPrint() {
}

StatisticPrint.fn = {
    init: function () {
        StatisticPrint.fn.statisticLazyLoad.call(this);

        if($('.bonprinter').length) {
            StatisticPrint.fn.printProcessing.call(this);
        }
    },

    statisticLazyLoad: function(){
        if($('.statistic-lazyload').length) {
            $('.statistic-lazyload').map(function(){
                var dashboardInit = $(this);
                var autoload = dashboardInit.data('autoload');

                if(autoload == 1) {
                    dashboardInit.attr('data-autoload', 0);

                    var url = dashboardInit.data('route');
                    var _token = $('meta[name="csrf-token"]').attr('content');
                    var dateRange = dashboardInit.find('.date-range-picker input[name="date_range"]').val();
                    var startDateRange = dashboardInit.find('.date-range-picker input[name="start_time"]').val();
                    var endDateRange = dashboardInit.find('.date-range-picker input[name="end_time"]').val();
                    var timezone = $('.auto-detect-timezone').val();
                    var data = {
                        _token: _token,
                        timezone: timezone,
                        date_range: dateRange,
                        start_time: startDateRange,
                        end_time: endDateRange
                    };

                    $('body').loading('toggle');

                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: data,
                        dataType: 'json',
                    }).success(function (response) {
                        if (response.success == true) {
                            $('#statistic-list').empty().append(response.data.view);
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

    printProcessing: function() {
        $(document).on('click', '.bonprinter', function(){
            $('body').loading('toggle');

            var _this = $(this);
            var url = _this.data('url');
            var _token = $('meta[name="csrf-token"]').attr('content');
            var filterDate = $('.ir-only-date-range').val();
            var timezone = $('.auto-detect-timezone').val();
            var dateRange = $('.date-range-picker input[name="date_range"]').val();
            var startDateRange = $('.date-range-picker input[name="start_time"]').val();
            var endDateRange = $('.date-range-picker input[name="end_time"]').val();

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: _token,
                    bon_printer: true,
                    filter_date: filterDate,
                    timezone: timezone,
                    date_range: dateRange,
                    start_time: startDateRange,
                    end_time: endDateRange
                },
                dataType: 'json',
            }).success(function (response) {
                if (response.success != true) {
                    toastr.error(response.message);
                }
            }).always(function () {
                $('body').loading('toggle');
            });
        });
    },

    rule: function () {
        $(document).ready(function () {
            StatisticPrint.fn.init.call(this);
        });
    },
};

StatisticPrint.fn.rule();