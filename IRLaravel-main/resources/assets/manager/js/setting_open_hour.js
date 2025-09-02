function SettingOpenHour() {
}

SettingOpenHour.fn = {
    init: function () {
        if($('.opening-hour').length) {
            SettingOpenHour.fn.resetWhenOpenHoliday.call(this);
            SettingOpenHour.fn.addNewHolidayTheFirstTime.call(this);
            SettingOpenHour.fn.addHoliday.call(this);
            SettingOpenHour.fn.removeHoliday.call(this);
            SettingOpenHour.fn.submitHoliday.call(this);
            SettingOpenHour.fn.enableChangeTime.call(this);
            SettingOpenHour.fn.addMoreTime.call(this);
            SettingOpenHour.fn.removeTime.call(this);
            SettingOpenHour.fn.handleOpenHourChangeTimeSlots.call(this);
            SettingOpenHour.fn.dateRange.call(this);
            SettingOpenHour.fn.changeInputTypeTime.call(this);
        }
    },

    // Check holiday
    resetWhenOpenHoliday: function(){
        $(document).on('click', '#id-holiday-exception', function(){
            var holidayEmpty = $(this).data('holiday_empty');

            if(holidayEmpty) {
                $('#holiday_exception').find('.modal-body .holiday-row').remove();
                $('.empty-holiday').show();
                $('.exist-holiday').hide();
            }
        });
    },

    addNewHolidayTheFirstTime: function(){
        $(document).on('click', '.add-new-holiday-empty', function(){
            $('.empty-holiday').hide();
            $('.exist-holiday').show();
            $('.add-holiday').trigger('click');
        });
    },

    addHoliday: function() {
        $(document).on('click', '.add-holiday', function(){
            var form = $(this).closest('form');
            var modalBody = form.find('.modal-body');
            var holidayRow = $('.holiday-item-default').clone();
            var maxRow = 0;

            modalBody.find('.holiday-row').map(function(){
                var rowNumber = $(this).data('row');

                if(rowNumber > maxRow) {
                    maxRow = rowNumber;
                }
            });

            var holidayCheck = holidayRow.find('.holiday-row');
            var newRow = maxRow + 1;
            var label = Lang.get('setting_open_hour.holiday');

            holidayCheck.attr('data-row', newRow);
            holidayCheck.find('.holiday-lbl').empty().html(label + ' ' + newRow + ':');
            holidayCheck.find('.holiday-id').attr('name', 'holiday['+ newRow +'][id]');
            holidayCheck.find('.ir-only-date-range').attr('name', 'holiday['+ newRow +'][date_range]');
            holidayCheck.find('.range_start_date').attr('name', 'holiday['+ newRow +'][start_time]');
            holidayCheck.find('.range_end_date').attr('name', 'holiday['+ newRow +'][end_time]');
            holidayCheck.find('.holiday-textarea').attr('name', 'holiday['+ newRow +'][description]');
            modalBody.append(holidayRow.html());

            var range = $('.holiday-row[data-row="'+ newRow +'"] .ir-only-date-range');
            SettingOpenHour.fn.dateRange(range);
        });
    },

    removeHoliday: function() {
        $(document).on('click', '.remove-holiday-exception', function() {
            $(this).closest('.row').remove();
        });
    },

    submitHoliday: function() {
        $('.holiday-exception').map(function(){
           $(this).validate({
               onkeyup: false,
               onfocusout: false,
               rules: {
                   'date_range[]': {
                       customRequired: true
                   },
                   'description[]': {
                       customRequired: true
                   }
               },
               submitHandler: function(form) {
                   SettingOpenHour.fn.formHoliday.call(this, $(form));
               }
           });
        });
    },

    formHoliday: function(selfForm){
        if (selfForm.valid()) {
            var url = selfForm.attr('action');
            var method = selfForm.attr('method');
            var data = selfForm.serializeArray();

            $('body').loading('toggle');

            $.ajax({
                url: url,
                type: method,
                data: data
            }).success(function (response) {
                if(response.success == true) {
                    location.reload();
                    $('#holiday_exception').modal('hide');
                } else {
                    if(response.data) {
                        MainShared.fn.showToastError(response);
                    }
                }
            }).error(function(XMLHttpRequest, textStatus, errorThrown) {
                var response = XMLHttpRequest.responseJSON;

                if(response.data) {
                    MainShared.fn.showToastError(response);
                }
            }).always(function() {
                $('body').loading('toggle');
            });
        }
    },

    enableChangeTime: function() {
        $(document).on('click', '.day-time-ip', function() {
            $(this).removeAttr('readOnly');
        });
    },

    addMoreTime: function() {
        $(document).on('click', '.add-more-time', function(){
            var _this = $(this);
            var dayTime = _this.closest('.day-time');
            var day = dayTime.data('day');
            var currentRowTime = _this.closest('.day-time-item');
            var defaultRowTime = $('.day-time-item-default').clone();
            var inputTime = dayTime.find('.day-time-ip');

            defaultRowTime.find('.day-time-ip').attr('name', 'start_end_time['+ day +'][]');
            defaultRowTime.find('.day-time-id').attr('name', 'slot_id['+ day +'][]');

            if(!inputTime.length) {
                currentRowTime.remove();
                dayTime.append(defaultRowTime.html());
                dayTime.find('.day-time-item').last().find('.day-time-ip').focus();
                dayTime.find('.day-time-item .start-time').trigger('input').trigger('focus');
            } else {
                currentRowTime.after(defaultRowTime.html());
                currentRowTime.next('.day-time-item').find('.day-time-ip').focus();
                currentRowTime.next('.day-time-item').find('.start-time').trigger('input').trigger('focus');
            }
        });
    },

    removeTime: function() {
        $(document).on('click', '.remove-time', function(){
            var _this = $(this);
            var dayTimeItem = _this.closest('.day-time-item');
            var dayTime = _this.closest('.day-time');
            var selfForm = _this.closest('form');
            var id = dayTimeItem.find('.day-time-id');

            dayTimeItem.remove();

            if(id.val() != '0') {
                SettingOpenHour.fn.submitOpenHourChangeTimeSlots(selfForm);
            }

            if(!dayTime.find('.day-time-item').length) {
                var defaultRowTime = $('.day-time-item-default').clone();
                defaultRowTime.find('.day-time-ip').remove();
                defaultRowTime.find('.time-area').append('<div class="text-day-off">'+ Lang.get('setting_open_hour.closed')+'</div>');
                defaultRowTime.find('.remove-time').remove();
                dayTime.append(defaultRowTime.html());
            }
        });
    },

    // Check main form
    formOpenHourChangeActive: function(){
        var _this = $(this);
        var selfForm = _this.closest('form');
        var url = selfForm.attr('action');
        var method = selfForm.attr('method');
        var data = selfForm.serializeArray();

        $('body').loading('toggle');

        $.ajax({
            url: url,
            type: method,
            data: data
        }).success(function (response) {
            if(response.success != true && response.data) {
                MainShared.fn.showToastError(response);
            }
        }).error(function(XMLHttpRequest, textStatus, errorThrown) {
            var response = XMLHttpRequest.responseJSON;

            if(response.data) {
                MainShared.fn.showToastError(response);
            }
        }).always(function() {
            $('body').loading('toggle');
        });
    },

    handleOpenHourChangeTimeSlots: function() {
        $(document).on('keypress', '.day-time-ip, .setting-open-hour.auto-submit', function(e) {
            if(e.which == 13) {
                $(this).trigger('blur');
            }
        });

        $(document).on('blur', '.day-time-ip, .setting-open-hour.auto-submit', function() {
            var selfForm = $(this).closest('form');
            SettingOpenHour.fn.submitOpenHourChangeTimeSlots(selfForm);
        });
    },

    submitOpenHourChangeTimeSlots: function(selfForm) {
        var flag = SettingOpenHour.fn.validateForm(selfForm);

        if(flag == true) {
            var url = selfForm.attr('action');
            var method = selfForm.attr('method');
            var data = selfForm.serializeArray();

            $('body').loading('toggle');

            $.ajax({
                url: url,
                type: method,
                data: data
            }).success(function (response) {
                if(response.success == true) {
                    var tabPanel = selfForm.closest('.tab-pane');
                    selfForm.remove();
                    tabPanel.append(response.data.view);
                } else {
                    if(response.data) {
                        MainShared.fn.showToastError(response);
                    }
                }
            }).error(function(XMLHttpRequest, textStatus, errorThrown) {
                var response = XMLHttpRequest.responseJSON;

                if(response.data) {
                    MainShared.fn.showToastError(response);
                }
            }).always(function() {
                $('body').loading('toggle');
            });
        }
    },

    validateForm: function(form) {
        var flag = true;

        flag = SettingOpenHour.fn.validateFormat(flag, form);
        flag = SettingOpenHour.fn.validateRangeTime(flag, form);

        return flag;
    },

    validateFormat: function(flag, form) {
        form.find('.day-time-ip').map(function() {
            var _this = $(this);
            var readOnly = _this.attr('readonly');

            if(typeof readOnly == 'undefined') {
                var value = _this.val();
                var check = /^([01]\d|2[0-3]):([0-5]\d) - ([01]\d|2[0-3]):([0-5]\d)/.test(value);

                if(!check) {
                    flag = false;
                    _this.addClass('error');
                } else {
                    var timeSplit = value.split(' - ');
                    var startTime = '2020-01-01 '+ timeSplit[0] + ':00';
                    var endTime = '2020-01-01 '+ timeSplit[1] + ':00';

                    if(!moment(endTime).isAfter(startTime)) {
                        flag = false;
                        _this.addClass('error');
                    } else {
                        _this.removeClass('error');
                    }
                }
            }
        });

        return flag;
    },

    validateRangeTime: function(flag, form) {
        form.find('.day-item').map(function() {
            var _this = $(this);
            var arrs = [];

            _this.find('.day-time-ip').map(function(){
                var _subThis = $(this);
                var value = _subThis.val();
                var timeSplit = value.split(' - ');
                arrs.push([timeSplit[0], timeSplit[1]]);
            });

            if(SettingOpenHour.fn.checkOverlap(arrs) == true) {
                flag = false;

                _this.find('.day-time-ip').map(function(){
                    var _subThis = $(this);
                    var _parent = _subThis.parent();
                    _subThis.addClass('error');
                    _parent.find('.timeNew').addClass('error');
                });
            } else {
                _this.find('.day-time-ip').map(function(){
                    var _subThis = $(this);
                    var _parent = _subThis.parent();
                    _subThis.removeClass('error');
                    _parent.find('.timeNew').removeClass('error');
                });
            }
        });

        return flag;
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
                minDate: now,
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
                    SettingOpenHour.fn.setDefautRangeInput(orderRange, startDate, endDate);
                }
            }

            _this.on('apply.daterangepicker', function(ev, picker) {
                _this.val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                SettingOpenHour.fn.setDefautRangeInput(orderRange, picker.startDate, picker.endDate);
            });

            _this.on('cancel.daterangepicker', function(ev, picker) {
                _this.data('daterangepicker').setStartDate(startDate);
                _this.data('daterangepicker').setEndDate(endDate);
                _this.val(startDate.format('DD/MM/YYYY') + ' - ' + endDate.format('DD/MM/YYYY'));
                SettingOpenHour.fn.setDefautRangeInput(orderRange, startDate, endDate);
            });
        });
    },

    setDefautRangeInput: function(orderRange, startDate, endDate) {
        if(orderRange.length) {
            orderRange.find('.range_start_date').val(startDate.format('YYYY-MM-DD'));
            orderRange.find('.range_end_date').val(endDate.format('YYYY-MM-DD'));
        }
    },

    checkOverlap: function(timeSegments) {
        if (timeSegments.length === 1) return false;

        timeSegments.sort((timeSegment1, timeSegment2) =>
            timeSegment1[0].localeCompare(timeSegment2[0])
        );

        for(var i = 0; i < timeSegments.length - 1; i++) {
            const currentEndTime = timeSegments[i][1];
            const nextStartTime = timeSegments[i + 1][0];

            if (currentEndTime > nextStartTime) {
                return true;
            }
        }

        return false;
    },

    changeInputTypeTime: function() {
        $(document).on('focus input', 'input[name=start_time]', function() {
            var parent = $(this).parent();
            var times = $(this).val() + ' - ' + parent.find('input[name=end_time]').val();
            parent.find('.day-time-ip').val(times);
        });

        $(document).on('focus input', 'input[name=end_time]', function() {
            var parent = $(this).parent();
            var times = parent.find('input[name=start_time]').val() + ' - ' + $(this).val();
            parent.find('.day-time-ip').val(times);
        });

        // When blur input
        $(document).on('blur', 'input[name=start_time]', function() {
            var parent = $(this).parent();
            parent.find('.day-time-ip').trigger('blur');
        });

        $(document).on('blur', 'input[name=end_time]', function() {
            var parent = $(this).parent();
            parent.find('.day-time-ip').trigger('blur');
        });
    },

    rule: function () {
        $(document).ready(function () {
            SettingOpenHour.fn.init.call(this);
        });
    },
};

SettingOpenHour.fn.rule();
