var beforeSend = false;
var timeToRefresh = 50000; // Default 50s

function Order() {
}

Order.fn = {
    init: function () {
        if(typeof Order.totalOrder == 'undefined') {
            Order.totalOrder = 0;
        }

        Order.fn.autoSubmit.call(this);
        Order.fn.orderDateRange.call(this);
        Order.fn.checkDay.call(this);
        Order.fn.markNoShow.call(this);
        Order.fn.connectorPushOrder(this);
        Order.fn.loadOrderList.call(this);
        Order.fn.clickExpandOrder.call(this);
        Order.fn.manualConfirmOrder.call(this);
        Order.fn.manualCheckedCashFullyPaid.call(this);
        Order.fn.sendSms.call(this);
    },

    clickExpandOrder: function() {
        Order.fn.refreshOrderList.call(this);

        $(document).on('click', '.has-expand', function (e) {
            clearInterval(Order.fn.timerId);
            timeToRefresh = 50000; // Reset time to 50s
            Order.fn.refreshOrderList()
        });
    },

    refreshOrderList: function() {
        if($('#manager-order-list').length) {
            Order.fn.timerId = setInterval(function(){
                Order.fn.loadOrderList.call(this, true);
            }, timeToRefresh);
        }
    },

    loadOrderList: function(refresh) {
        if(typeof refresh == 'undefined') {
            var refresh = false;
        }

        var orderList = $('#manager-order-list');
        var autoload = orderList.data('autoload');
        var url = orderList.data('route');

        if(autoload == 1 || refresh == true) {
            orderList.attr('data-autoload', 0);

            var _token = $('meta[name="csrf-token"]').attr('content');
            var dateRange = orderList.find('.order-date-range .ir-date-range').val();
            var startDateRange = orderList.find('.order-date-range .range_start_date').val();
            var endDateRange = orderList.find('.order-date-range .range_end_date').val();
            var timezone = $('.auto-detect-timezone').val();
            var data = {
                _token: _token,
                timezone: timezone,
                date_range: dateRange,
                range_start_date: startDateRange,
                range_end_date: endDateRange
            };

            if(refresh == false) {
                $('body').loading('toggle');
            }
            if(!beforeSend) {
                beforeSend = true;
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: data,
                    dataType: 'json',
                }).success(function (response) {
                    if (response.success == true) {
                        if(typeof response.data.totalOrder != 'undefined' &&
                            response.data.totalOrder != Order.totalOrder) {
                            Order.totalOrder = response.data.totalOrder;

                            if(refresh == true) {
                                var sound = $('#welcome-sound').val();
                                new Audio(sound).play();
                            }
                        }

                        $('.order-list-table').empty().append(response.data.view);
                        MainShared.fn.convertUtcToLocalTime.call(this);
                        MainShared.fn.convertUtcToLocalTimeOrder.call(this);
                    }
                    beforeSend = false;
                }).error(function (XMLHttpRequest, textStatus, errorThrown) {
                    beforeSend = false;
                    console.log(XMLHttpRequest, textStatus, errorThrown);
                }).always(function () {
                    beforeSend = false;
                    if(refresh == false) {
                        $('body').loading('toggle');
                    }
                });
            }
        }
    },

    autoSubmit: function() {
        $(document).on('change', '.order-auto-submit', function(){
            var form = $(this).closest('form');

            if(form.length) {
                form.submit();
            }
        });
    },

    calendarAutoSubmit: function(_this) {
        var form = _this.closest('form');

        if(form.length) {
            form.submit();
        }
    },

    setDefautRangeInput: function(startDate, endDate) {
        var orderRange = $('.order-date-range');

        if(orderRange.length) {
            orderRange.find('.range_start_date').val(startDate.format('YYYY-MM-DD HH:mm:ss'));
            orderRange.find('.range_end_date').val(endDate.format('YYYY-MM-DD HH:mm:ss'));
        }
    },

    orderDateRange: function () {
        var startDate = moment().startOf('day');
        var endDate = moment().startOf('day').add(23, 'hour').add(59, 'minute').add(59, 'second');
        var orderRange = $('.order-date-range');

        let drpOptions = {
            drops: 'auto',
            timePicker: true,
            timePicker24Hour: true,
            autoUpdateInput: true,
            startDate: startDate,
            endDate: endDate,
            locale: {
                firstDay: 1,
                format: 'DD/MM/YYYY HH:mm',
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

        $('.order-date-range .ir-date-range').daterangepicker(drpOptions);

        if(orderRange.length) {
            var startDateOrigin = orderRange.find('.range_start_date').val();
            var endDateOrigin = orderRange.find('.range_end_date').val();

            if(startDateOrigin != '' && endDateOrigin != '') {
                $('.order-date-range .ir-date-range').data('daterangepicker').setStartDate(moment(startDateOrigin));
                $('.order-date-range .ir-date-range').data('daterangepicker').setEndDate(moment(endDateOrigin));
            } else {
                Order.fn.setDefautRangeInput(startDate, endDate);
            }
        }

        $('.order-date-range .ir-date-range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY HH:mm') + ' - ' + picker.endDate.format('DD/MM/YYYY HH:mm'));
            Order.fn.setDefautRangeInput(picker.startDate, picker.endDate);
            Order.fn.calendarAutoSubmit($(this));
        });

        $('.order-date-range .ir-date-range').on('cancel.daterangepicker', function(ev, picker) {
            $('.order-date-range .ir-date-range').data('daterangepicker').setStartDate(startDate);
            $('.order-date-range .ir-date-range').data('daterangepicker').setEndDate(endDate);
            $(this).val(startDate.format('DD/MM/YYYY HH:mm') + ' - ' + endDate.format('DD/MM/YYYY HH:mm'));
            Order.fn.setDefautRangeInput(startDate, endDate);
            Order.fn.calendarAutoSubmit($(this));
        });

        $('.order-date-range .ir-date-range').on('hide.daterangepicker', function(ev, picker) {
            window.location.reload();
        });
    },

    checkDay: function() {
        var startRange = $('.range_start_date');
        var endRange = $('.range_end_date');

        if(startRange.length && endRange.length) {
            var startDate = moment(startRange.val()).format('YYYY-MM-DD');
            var endDate = moment(endRange.val()).format('YYYY-MM-DD');
            var today = moment().format('YYYY-MM-DD');
            var tomorrow = moment().add(1,'days').format('YYYY-MM-DD');
            var yesterday = moment().add(-1, 'days').format('YYYY-MM-DD');
            var checkDay = $('.check-day');

            if(startDate == endDate) {
                if(startDate == today) {
                    checkDay.empty().text(Lang.get('common.today'));
                } else if(startDate == tomorrow) {
                    checkDay.empty().text(Lang.get('common.tomorrow'));
                } else if(startDate == yesterday) {
                    checkDay.empty().text(Lang.get('common.yesterday'));
                } else {
                    checkDay.empty().text(moment(startRange.val()).locale(Lang.getLocale()).format('dddd DD MMMM YYYY'));
                }
            } else {
                checkDay.empty().text(moment(startRange.val()).format('DD/MM/YYYY') + ' - ' + moment(endRange.val()).format('DD/MM/YYYY'));
            }

        }
    },

    markNoShow: function() {
        $(document).on('click', '.mark-no-show', function(){
            var _this = $(this);
            var url = _this.data('url');
            var _token = $('meta[name="csrf-token"]').attr('content');
            var subTitle = _this.data('subtitle');
            var yesButton = _this.data('yes');
            var noButton = _this.data('no');

            Swal.fire({
                title: subTitle,
                width: 512,
                padding: '43px 60px 30px 60px',
                showCloseButton: true,
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: noButton,
                cancelButtonText: yesButton,
            }).then((result) => {
                if (result.isDismissed && result.dismiss !== Swal.DismissReason.close) {
                    $('body').loading('toggle');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {_token: _token},
                        dataType: 'json',
                    }).done(function (response) {
                        if (response.success === true) {
                            _this.closest('.col-item').removeClass('open');
                            _this.closest('.list-body-item').find('.no-show-label').show();
                            _this.remove();
                            var title = Lang.get('common.success');
                        } else {
                            var title = Lang.get('common.error');
                        }
                        Swal.fire({
                            title: '<span class="ir-popup-title">' + title + '</span>',
                            html: '<span class="ir-popup-content">' + response.message + '</span>',
                            width: 512,
                            padding: '43px 60px 30px 60px',
                            showConfirmButton: false,
                            showCloseButton: true,
                            showCancelButton: true,
                            cancelButtonText: Lang.get('common.close')
                        });
                    }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                        console.log(XMLHttpRequest, textStatus, errorThrown);
                    }).always(function () {
                        $('body').loading('toggle');
                    });
                }
            });
        });
    },

    sendSms: function() {
        $(document).on('click', '.send-sms', function(){
            var _this = $(this);
            var url = _this.data('url');
            var _token = $('meta[name="csrf-token"]').attr('content');
            var subTitle = _this.data('subtitle');
            var yesButton = _this.data('yes');
            var noButton = _this.data('no');
            var workspace_id = _this.data('workspace_id');
            var status = _this.data('status');
            var foreign_model = _this.data('foreign_model');
            var foreign_id = _this.data('foreign_id');
            var message = _this.data('message');
            var data = {
                _token: _token,
                workspace_id: workspace_id,
                status: status,
                foreign_model: foreign_model,
                foreign_id: foreign_id,
                message: message,
            };

            Swal.fire({
                title: subTitle,
                width: 512,
                padding: '43px 60px 30px 60px',
                showCloseButton: true,
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: noButton,
                cancelButtonText: yesButton,
            }).then((result) => {
                if (result.isDismissed && result.dismiss !== Swal.DismissReason.close) {
                    $('body').loading('toggle');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                    }).done(function (response) {
                        if (response.success === true) {
                            var title = Lang.get('common.success');
                        } else {
                            var title = Lang.get('common.error');
                        }
                        Swal.fire({
                            title: '<span class="ir-popup-title">' + title + '</span>',
                            html: '<span class="ir-popup-content">' + response.message + '</span>',
                            width: 512,
                            padding: '43px 60px 30px 60px',
                            showConfirmButton: false,
                            showCloseButton: true,
                            showCancelButton: true,
                            cancelButtonText: Lang.get('common.close')
                        });
                    }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                        console.log(XMLHttpRequest, textStatus, errorThrown);
                    }).always(function () {
                        $('body').loading('toggle');
                    });
                }
            });
        });
    },

    manualConfirmOrder: function() {
        $(document).on('click', '.manual-confirm-order', function(){
            var _this = $(this);
            var url = _this.data('url');
            var _token = $('meta[name="csrf-token"]').attr('content');
            var subTitle = _this.data('subtitle');
            var yesButton = _this.data('yes');
            var noButton = _this.data('no');

            console.log(123);

            Swal.fire({
                title: subTitle,
                width: 512,
                padding: '43px 60px 30px 60px',
                showCloseButton: true,
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: noButton,
                cancelButtonText: yesButton,
            }).then((result) => {
                if (result.isDismissed && result.dismiss !== Swal.DismissReason.close) {
                    $('body').loading('toggle');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {_token: _token},
                        dataType: 'json',
                    }).done(function (response) {
                        if (response.success === true) {
                            console.log(response.data.confirmedDropdown);
                            _this.closest('.manual-confirm-order').css({
                                'opacity': '50%',
                                'pointer-events': 'none'
                            });
                            _this.closest('.list-body-item').find('.table_actions').html(response.data.confirmedDropdown);
                            var title = Lang.get('common.success');
                        } else {
                            var title = Lang.get('common.error');
                        }
                        Swal.fire({
                            title: '<span class="ir-popup-title">' + title + '</span>',
                            html: '<span class="ir-popup-content">' + response.message + '</span>',
                            width: 512,
                            padding: '43px 60px 30px 60px',
                            showConfirmButton: false,
                            showCloseButton: true,
                            showCancelButton: true,
                            cancelButtonText: Lang.get('common.close')
                        });
                    }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                        console.log(XMLHttpRequest, textStatus, errorThrown);
                    }).always(function () {
                        $('body').loading('toggle');
                    });
                }
            });
        });
    },

    manualCheckedCashFullyPaid: function() {
        $(document).on('click', '.manual-checked-fully-paid-cash', function(){
            var _this = $(this);
            var url = _this.data('url');
            var _token = $('meta[name="csrf-token"]').attr('content');
            var subTitle = _this.data('subtitle');
            var yesButton = _this.data('yes');
            var noButton = _this.data('no');

            Swal.fire({
                title: subTitle,
                width: 512,
                padding: '43px 60px 30px 60px',
                showCloseButton: true,
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: yesButton,
                cancelButtonText: noButton,
            }).then((result) => {
                if (result.isConfirmed && result.dismiss !== Swal.DismissReason.close) {
                    $('body').loading('toggle');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {_token: _token},
                        dataType: 'json',
                    }).done(function (response) {
                        if (response.success === true) {
                            _this.closest('.list-body-item')
                                .find('.row-main-content .bubble-paid-status').css({
                                'background-color': 'green',
                            });
                            _this.closest('.list-body-item')
                                .find('.calculate_total_paid').text(response.data.total_paid);
                            _this.closest('.manual-checked-fully-paid-cash').css({
                                'opacity': '50%',
                                'pointer-events': 'none'
                            });
                            var title = Lang.get('common.success');
                        } else {
                            var title = Lang.get('common.error');
                        }
                        Swal.fire({
                            title: '<span class="ir-popup-title">' + title + '</span>',
                            html: '<span class="ir-popup-content">' + response.message + '</span>',
                            width: 512,
                            padding: '43px 60px 30px 60px',
                            showConfirmButton: false,
                            showCloseButton: true,
                            showCancelButton: true,
                            cancelButtonText: Lang.get('common.close')
                        });
                    }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                        console.log(XMLHttpRequest, textStatus, errorThrown);
                    }).always(function () {
                        $('body').loading('toggle');
                    });
                }
            });
        });
    },

    connectorPushOrder: function() {
        $(document).on('click', '.connector-push-order', function(){
            var _this = $(this);
            var url = _this.data('url');
            var _token = $('meta[name="csrf-token"]').attr('content');

            $('body').loading('toggle');

            $.ajax({
                url: url,
                type: 'POST',
                data: {_token: _token},
                dataType: 'json',
            }).success(function (response) {
                if (response.success == true) {
                    var title = Lang.get('common.success');
                } else {
                    var title = Lang.get('common.error');
                }

                Swal.fire({
                    title: '<span class="ir-popup-title">' + title + '</span>',
                    html: '<span class="ir-popup-content">' + response.message + '</span>',
                    width: 512,
                    padding: '43px 60px 30px 60px',
                    showConfirmButton: false,
                    showCloseButton: true,
                    showCancelButton: true,
                    cancelButtonText: Lang.get('common.close')
                });
            }).error(function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(XMLHttpRequest, textStatus, errorThrown);
            }).always(function () {
                $('body').loading('toggle');
            });
        });
    },

    rule: function () {
        $(document).ready(function () {
            Order.fn.init.call(this);
        });
    },
};

Order.fn.rule();
