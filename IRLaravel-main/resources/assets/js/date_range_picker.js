function DateRangePicker() {
}

DateRangePicker.fn = {
    init: function () {
        if($('.date-range-picker').length) {
            DateRangePicker.fn.dateRange.call(this);
        }
    },

    /**
     * Get date range picker locale
     *
     * @param drpOptions
     * @returns {*}
     */
    getLocaleDateRangePicker: function(drpOptions) {
        if (typeof drpOptions['locale'] === 'undefined') {
            drpOptions['locale'] = {
                daysOfWeek: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
                monthNames: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc"]
            };
        }

        // Locale days of week
        let localeDaysOfWeek = JSON.parse($('#localeDaysOfWeek').val() ?? "{}");
        if (localeDaysOfWeek) {
            drpOptions['locale']['daysOfWeek'] = localeDaysOfWeek;
        }

        // Locale month names
        let localeMonthNames = JSON.parse($('#localeMonthNames').val() ?? "{}");
        if (localeMonthNames) {
            drpOptions['locale']['monthNames'] = localeMonthNames;
        }

        return drpOptions;
    },

    dateRange: function (element) {
        if(typeof element == 'undefined') {
            element = $('.ir-only-date-range');
        }

        var now = moment();
        var startDate = moment().startOf('day');
        var endDate = moment().startOf('day').add(23, 'hour').add(59, 'minute').add(59, 'second');

        element.map(function(){
            var _this = $(this);

            let drpOptions = {
                drops: 'auto',
                timePicker: false,
                timePicker24Hour: false,
                autoUpdateInput: true,
                startDate: startDate,
                endDate: endDate,
                locale: {
                    firstDay: 1,
                    format: 'DD/MM/YYYY',
                    cancelLabel: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
                        '<rect width="24" height="24" rx="2" fill="#808080"/>\n' +
                        '<path d="M9 14L4 9L9 4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>\n' +
                        '<path d="M20 20V13C20 11.9391 19.5786 10.9217 18.8284 10.1716C18.0783 9.42143 17.0609 9 16 9H4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>\n' +
                        '</svg>',
                    applyLabel: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
                        '<rect width="24" height="24" rx="2" fill="#B5B268"/>\n' +
                        '<path d="M20 6L9 17L4 12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>\n' +
                        '</svg>'
                }
            };

            if (typeof DateRangePicker !== 'undefined' && typeof DateRangePicker.fn.getLocaleDateRangePicker === 'function') {
                drpOptions = DateRangePicker.fn.getLocaleDateRangePicker.call(this, drpOptions);
            }

            _this.daterangepicker(drpOptions);

            var orderRange = _this.closest('.area-date-range');

            if(orderRange.length) {
                var startDateOrigin = orderRange.find('.range_start_date').val();
                var endDateOrigin = orderRange.find('.range_end_date').val();

                if(startDateOrigin != '' && endDateOrigin != '') {
                    var formatHidden = 'YYYY-MM-DD';
                    var format = 'DD/MM/YYYY';
                    var convertStartDate = moment.utc(startDateOrigin).format(formatHidden);
                    var convertEndDate = moment.utc(endDateOrigin).format(formatHidden);

                    orderRange.find('.range_start_date').val(convertStartDate);
                    orderRange.find('.range_end_date').val(convertEndDate);
                    _this.data('daterangepicker').setStartDate(moment(convertStartDate));
                    _this.data('daterangepicker').setEndDate(moment(convertEndDate));
                    _this.val(moment(convertStartDate).format(format) + ' - ' + moment(convertEndDate).format(format));
                } else {
                    DateRangePicker.fn.setDefautRangeInput(orderRange, startDate, endDate);
                }
            }

            _this.on('apply.daterangepicker', function(ev, picker) {
                _this.val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                DateRangePicker.fn.setDefautRangeInput(orderRange, picker.startDate, picker.endDate);
                _this.closest('form').submit();
            });

            _this.on('cancel.daterangepicker', function(ev, picker) {
                _this.data('daterangepicker').setStartDate(startDate);
                _this.data('daterangepicker').setEndDate(endDate);
                _this.val(startDate.format('DD/MM/YYYY') + ' - ' + endDate.format('DD/MM/YYYY'));
                DateRangePicker.fn.setDefautRangeInput(orderRange, startDate, endDate);
                _this.closest('form').submit();
            });
            
            _this.on('hide.daterangepicker', function(ev, picker) {
                window.location.reload();
            });
        });
    },

    setDefautRangeInput: function(orderRange, startDate, endDate) {
        if(orderRange.length) {
            orderRange.find('.range_start_date').val(startDate.format('YYYY-MM-DD'));
            orderRange.find('.range_end_date').val(endDate.format('YYYY-MM-DD'));
        }
    },

    rule: function () {
        $(document).ready(function () {
            DateRangePicker.fn.init.call(this);
        });
    },
};

DateRangePicker.fn.rule();