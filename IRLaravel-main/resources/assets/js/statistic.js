function Statistic() {
}

Statistic.fn = {
    init: function () {},

    convertOrderData: function(orders, onlyDate) {
        var orderChartData = [];
        var revenueChartData = [];
        var format = 'YYYY-MM-DD HH:mm:ss';

        if(onlyDate == 1) {
            format = 'YYYY-MM-DD';
        }

        $.each(orders, function(index, order) {
            var localTime = moment.utc(order.date_time).local().format(format);

            if(typeof orderChartData[localTime] == 'undefined') {
                orderChartData[localTime] = 1;
                revenueChartData[localTime] = parseFloat(parseFloat(order.total_price).toFixed(2));
            } else {
                orderChartData[localTime] = parseInt(orderChartData[localTime]) + 1;
                revenueChartData[localTime] = parseFloat(parseFloat(parseFloat(revenueChartData[localTime]) + parseFloat(order.total_price)).toFixed(2));
            }
        });

        var result = {
            orderChartData: orderChartData,
            revenueChartData: revenueChartData
        };

        return result;
    },

    setupCharts: function(element, yLabel, lineColor, serieTitle, serieDataJson, onlyDate) {
        var serieData = [];
        serieDataJson = Object.assign({}, serieDataJson);
        var xAxis = {
            type: 'datetime',
            labels: {
                format: '{value:%d/%m}'
            }
        };

        if(onlyDate == 1) {
            $.each(serieDataJson, function(date, value) {
                var dateSplit = date.split('-');
                date = Date.UTC(dateSplit[0], (dateSplit[1] - 1), dateSplit[2], 0, 0, 0);
                serieData.push([date, parseFloat(value)]);
            });
        } else {
            xAxis = {
                type: 'datetime',
                tickInterval: 1 * 3600 * 1000
            };

            $.each(serieDataJson, function(date, value) {
                var dateTimeSplit = date.split(' ');
                var dateSplit = (dateTimeSplit[0]).split('-');
                var timeSplit = (dateTimeSplit[1]).split(':');
                date = Date.UTC(dateSplit[0], (dateSplit[1] - 1), dateSplit[2], timeSplit[0], timeSplit[1], timeSplit[2]);
                serieData.push([date, parseFloat(value)]);
            });
        }

        // Get locale from <html lang
        let locale = $('html').attr('lang');

        let localeMonths = ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'];
        let localeWeekdays = ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag'];
        let localeShortMonths = ['jan', 'feb', 'mrt', 'apr', 'mei', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec'];
        let jqElement = $('#' + element);

        if (jqElement.data('locale-months')) {
            localeMonths = jqElement.data('locale-months');
        }

        if (jqElement.data('locale-weekdays')) {
            localeWeekdays = jqElement.data('locale-weekdays');
        }

        if (jqElement.data('locale-short-months')) {
            localeShortMonths = jqElement.data('locale-short-months');
        }

        // Ticket ITR-2013: Date format should be in Dutch (e.g. Vrijdag, 6 september 2024)
        Highcharts.setOptions({
            lang: {
                locale: locale,
                months: localeMonths,
                weekdays: localeWeekdays,
                shortMonths: localeShortMonths,
            }
        });

        Highcharts.chart(element, {
            chart: {
                type: 'spline'
            },
            title: false,
            xAxis: xAxis,
            yAxis: {
                title: false,
                labels: {
                    format: yLabel
                }
            },
            /*tooltip: {
                formatter: function() {
                    return Highcharts.dateFormat('%A, %e %B %Y', this.x);
                }
            },*/
            plotOptions: {
                spline: {
                    lineWidth: 4,
                    color: lineColor
                }
            },
            series: [{
                name: serieTitle,
                data: serieData
            }]
        });
    },

    rule: function () {
        $(document).ready(function () {
            Statistic.fn.init.call(this);
        });
    },
};

Statistic.fn.rule();