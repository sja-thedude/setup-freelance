function SettingTimeSlot() {
}

SettingTimeSlot.fn = {
    init: function () {
        if($('.time-slot').length) {
            SettingTimeSlot.fn.formValidate.call(this);
            SettingTimeSlot.fn.loadTheFirstTime.call(this);
            SettingTimeSlot.fn.changeTimeSlotDetailDate.call(this);
            SettingTimeSlot.fn.displayDatePicker.call(this);
            SettingTimeSlot.fn.triggerDate.call(this);
            SettingTimeSlot.fn.changeTab.call(this);
            SettingTimeSlot.fn.loadDisableMaximaal.call(this);
        }
    },

    checkDisableMaximaal: function(_this) {
        var val = _this.val();
        var checked = _this.is(':checked');
        var form = _this.closest('form');
        var fieldShouldDisable = form.find('.should-disable');

        if(checked) {
            if(fieldShouldDisable.length) {
                fieldShouldDisable.removeAttr('disabled');
                var select = fieldShouldDisable.find('select');

                if(select.length) {
                    select.removeAttr('disabled');
                }
            }
        } else {
            if(fieldShouldDisable.length) {
                fieldShouldDisable.attr('disabled', 'disabled');
                var select = fieldShouldDisable.find('select');

                if(select.length) {
                    select.attr('disabled', 'disabled');
                }
            }
        }
    },

    loadDisableMaximaal: function(_this) {
        $('.disable-fields').map(function(){
            SettingTimeSlot.fn.checkDisableMaximaal($(this));
        });

        $(document).on('change', '.disable-fields', function() {
            SettingTimeSlot.fn.checkDisableMaximaal($(this));
        });
    },

    // Check detail form
    displayDatePicker: function() {
        var activeTab = $('.nav-item-tab.active');
        var activeType = activeTab.data('type');

        $('.time-date-picker').hide();
        $('.time-date-picker[data-type="'+ activeType +'"]').show();
    },

    triggerDate: function() {
        $(document).on('click', '.time-slot-icon', function(){
            $(this).closest('.date-group').find('.time-slot-detail-date').datepicker("show");
        });
    },

    changeTab: function() {
        $(document).on('click', '.nav-item-tab', function(){
            SettingTimeSlot.fn.displayDatePicker.call(this);
        });
    },

    loadTimeSlotDetail: function(element, url, date, length, count){
        var token = $('meta[name="csrf-token"]').attr('content');
        var timezone = $('.auto-detect-timezone').val();
        var data = {
            _token: token,
            date: date,
            timezone: timezone
        };

        if((typeof length == 'undefined' && typeof count == 'undefined') || count == 1) {
            $('body').loading('toggle');
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: data
        }).success(function (response) {
            if(response.success == true) {
                element.removeClass('error');
                $('.dynamic-tab-content')
                    .find('[data-dynamic-id="'+ response.data.settingTimeSlot.type +'"]')
                    .empty()
                    .append(response.data.view);

                SettingTimeSlot.fn.validateDetailForm.call(this);
            } else {
                element.addClass('error');
            }
        }).error(function(XMLHttpRequest, textStatus, errorThrown) {
            element.addClass('error');
        }).always(function() {
            if((typeof length == 'undefined' && typeof count == 'undefined') || count == length) {
                $('body').loading('toggle');
            }
        });
    },

    loadTheFirstTime: function(){
        $(document).on('click', '[data-target="#time-slot-modal"]', function(){
            var length = $('.time-slot-detail-date').length;
            var count = 0;

            $('.time-slot-detail-date').datepicker('setDate', moment().format('DD/MM/YYYY'));
            $('.time-slot-detail-date').map(function(){
                count++;

                var _this = $(this);
                var date = _this.val();
                var url = _this.data('route');

                SettingTimeSlot.fn.loadTimeSlotDetail(_this, url, date, length, count);
            });
        });
    },

    changeTimeSlotDetailDate: function() {
        $(document).on('change', '.time-slot-detail-date', function(){
            var _this = $(this);
            var date = _this.val();
            var url = _this.data('route');

            SettingTimeSlot.fn.loadTimeSlotDetail(_this, url, date);
        });
    },

    validateDetailForm: function() {
        $('.time-slot-detail').map(function(){
            $(this).validate({
                onkeyup: false,
                onfocusout: false,
                submitHandler: function(form) {
                    SettingTimeSlot.fn.submitFormTimeSlotDetail.call(this, $(form));
                }
            });
        });

        $('.max-field').map(function(){
            jQuery(this).rules('add', {
                customRequired: true,
                integerFormat: true
            });
        });
    },

    autoSubmitTimeSlotDetail: function() {
        var _this = $(this);
        var form = _this.closest('form');

        SettingTimeSlot.fn.submitFormTimeSlotDetail.call(this, form);
    },

    submitFormTimeSlotDetail: function(selfForm) {
        if (selfForm.valid()) {
            var _this = $(this);
            var url = selfForm.attr('action');
            var method = selfForm.attr('method');
            var data = selfForm.serializeArray();
            var _data = $('input[data-item-id='+_this.data('item-id')+']').serializeArray();

            $('body').loading('toggle');

            $.ajax({
                url: url,
                type: method,
                data: _data
            }).success(function (response) {
                if(response.success != true) {
                    if(response.data) {
                        MainShared.fn.showErrorMessages(response, selfForm, true);
                    }
                }
            }).error(function(XMLHttpRequest, textStatus, errorThrown) {
                var response = XMLHttpRequest.responseJSON;

                if(response.data) {
                    MainShared.fn.showErrorMessages(response, selfForm, true);
                }
            }).always(function() {
                $('body').loading('toggle');
            });
        }
    },

    // Check main form
    autoSubmit: function() {
        var _this = $(this);
        var form = _this.closest('form');

        SettingTimeSlot.fn.submitFormTimeSlot.call(this, form);
    },

    submitFormTimeSlot: function(selfForm){
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
                if(response.success != true) {
                    if(response.data) {
                        MainShared.fn.showErrorMessages(response, selfForm, true);
                    }
                }
            }).error(function(XMLHttpRequest, textStatus, errorThrown) {
                var response = XMLHttpRequest.responseJSON;

                if(response.data) {
                    MainShared.fn.showErrorMessages(response, selfForm, true);
                }
            }).always(function() {
                $('body').loading('toggle');
            });
        }
    },

    formValidate: function() {
        $('.update-time-slot').map(function(){
            $(this).validate({
                onkeyup: false,
                onfocusout: false,
                rules: {
                    'order_per_slot': {
                        customRequired: true,
                        integerFormat: true
                    },
                    'max_price_per_slot': {
                        customRequired: true
                    },
                    'interval_slot': {
                        customRequired: true
                    },
                    'max_time': {
                        customRequired: true,
                        timeFormat: true
                    },
                    'max_before': {
                        customRequired: true
                    }
                },
                submitHandler: function(form) {
                    SettingTimeSlot.fn.submitFormTimeSlot.call(this, $(form));
                }
            });
        });
    },

    rule: function () {
        $(document).ready(function () {
            SettingTimeSlot.fn.init.call(this);
        });
    },
};

SettingTimeSlot.fn.rule();