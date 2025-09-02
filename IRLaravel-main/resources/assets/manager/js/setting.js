function Setting() {
}

Setting.fn = {
    init: function () {
        Setting.fn.submitUpdateFormProfile.call(this);
        Setting.fn.submitUpdateFormWorkspace.call(this);
        Setting.fn.submitUpdateFormSettingGenerals.call(this);
        Setting.fn.pickColor.call(this);
        Setting.fn.submitUpdateFormSettingPayments.call(this);
        Setting.fn.submitUpdateFormSettingPreferences.call(this);
        Setting.fn.addMoreDelivery.call(this);
        Setting.fn.deleteDelivery.call(this);
        Setting.fn.submitUpdateFormSettingDelivery.call(this);
        Setting.fn.submitUpdateFormSettingPrint.call(this);
        Setting.fn.addMorePrint.call(this);
    },

    autoSubmitGeneral: function () {
        var _this = $(this);
        var form = _this.closest('form');

        Setting.fn.formGeneral.call(this, form);
    },

    submitUpdateFormProfile: function () {
        $('.general-update-profile').map(function(){
            $(this).validate({
                onkeyup: function (element) {
                    $(element).valid();
                },
                rules: {
                    last_name: {
                        customRequired: true,
                        onlyText: true
                    },
                    first_name: {
                        customRequired: true,
                        onlyText: true
                    },
                    email: {
                        customRequired: true,
                        emailValidate: true
                    },
                    gsm: {
                        customRequired: true,
                        phoneValidate: true
                    }
                },
                submitHandler: function (form) {
                    Setting.fn.formGeneral.call(this, $(form));
                }
            });
        });
    },

    submitUpdateFormWorkspace: function () {
        $('.general-update-workspace').map(function(){
            $(this).validate({
                onkeyup: function (element) {
                    $(element).valid();
                },
                errorPlacement: function (error, element) {
                    var elem = $(element);
                    if (elem.hasClass('select2') || elem.hasClass('select2-type')) {
                        error.hide();
                        error.insertAfter(elem.closest('.form-group').find('.select2'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element, errorClass, validClass) {
                    var elem = $(element);
                    if (elem.hasClass('select2') || elem.hasClass('select2-type')) {
                        elem.closest('.form-group').find('.select2').addClass(errorClass);
                    } else {
                        elem.addClass(errorClass);
                    }
                },
                unhighlight: function (element, errorClass, validClass) {
                    var elem = $(element);
                    if (elem.hasClass('select2') || elem.hasClass('select2-type')) {
                        elem.closest('.form-group').find('.select2').removeClass(errorClass);
                    } else {
                        elem.removeClass(errorClass);
                    }
                },
                rules: {
                    'name': {
                        customRequired: true,
                    },
                    'btw_nr': {
                        customRequired: true,
                    },
                    'address': {
                        customRequired: true,
                    },
                    'types[]': {
                        maxlength: 6
                    }
                },
                submitHandler: function (form) {
                    Setting.fn.formGeneral.call(this, $(form));
                }
            });
        });
    },
    
    submitUpdateFormSettingGenerals: function () {
        $('.update-form-setting-generals').map(function(){
            $(this).validate({
                onkeyup: function (element) {
                    $(element).valid();
                },
                submitHandler: function (form) {
                    Setting.fn.formGeneral.call(this, $(form));
                }
            });
        });
    },
    
    submitUpdateFormSettingPayments: function () {
        $('.update-form-payment-methods').map(function(){
            $(this).validate({
                onkeyup: function (element) {
                    $(element).valid();
                },
                submitHandler: function (form) {
                    Setting.fn.formGeneral.call(this, $(form));
                }
            });
        });
    },
    
    submitUpdateFormSettingPreferences: function () {
        $('.update-form-preferences').map(function(){
            $(this).validate({
                onkeyup: function (element) {
                    $(element).valid();
                },
                rules: {
                    'takeout_min_time': {
                        onlyNumber: true
                    },
                    'delivery_min_time': {
                        onlyNumber: true
                    },
                    'takeout_day_order': {
                        onlyFullNumber: true,
                        maxlength: 14
                    },
                    'delivery_day_order': {
                        onlyFullNumber: true,
                        maxlength: 14
                    },
                    'mins_before_notify': {
                        onlyFullNumber: true
                    },
                },
                submitHandler: function (form) {
                    Setting.fn.formGeneral.call(this, $(form));
                }
            });
        });
    },
    
    submitUpdateFormSettingDelivery: function () {
        $('.update-form-delivery-conditions').map(function(){
            $(this).validate({
                onkeyup: function (element) {
                    $(element).valid();
                },
                submitHandler: function (form) {
                    Setting.fn.formGeneral.call(this, $(form));
                }
            });
        });
    },
    
    submitUpdateFormSettingPrint: function () {
        $('input[data-number=true]').keyup(function () {
            var value = $(this).val();
            
           if (/\D/g.test(value)) {
               $(this).val(value.replace(/\D/g, ''));
           }
        });
        
        $('.update-form-print').map(function(){
            $(this).validate({
                onkeyup: function (element) {
                    $(element).valid();
                },
                submitHandler: function (form) {
                    Setting.fn.formGeneral.call(this, $(form));
                }
            });
        });
    },

    formGeneral: function (selfForm) {
        if ($(selfForm).valid()) {
            // var selfForm = $(form);
            var url = selfForm.attr('action');
            var method = selfForm.attr('method');
            var submit = selfForm.find('[type="submit"]');
            var formData = new FormData(selfForm[0]);
            // var data = selfForm.serializeArray();
            $('body').loading('toggle');
            submit.attr('disabled', 'disabled');

            $.ajax({
                url: url,
                type: method,
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
            }).success(function (response) {
                if (response.success == true) {
                    MainShared.fn.formatNumberDecimal();
                    
                    //Hide error settings/general
                     if ($('#types').length) {
                        $('#types').closest('.form-group').find('.select2').removeClass('error');
                    }
                     
                     // Reload preferences page after submit holiday
                    if ($(".holiday-reload").length) {
                        window.location.reload();                        
                    }
                } else {
                    if (response.data) {
                        MainShared.fn.showErrorMessages(response, selfForm);
                    }
                }
            }).error(function (XMLHttpRequest, textStatus, errorThrown) {
                var response = XMLHttpRequest.responseJSON;
                
                if (response.data) {
                    MainShared.fn.showErrorMessages(response, selfForm);
                }
            }).always(function () {
                submit.removeAttr('disabled');
                $('body').loading('toggle');
            });
        }
    },

    pickColor: function () {
        $('#primary').ColorPicker({
            onChange: function (hsb, hex, rgb) {
                $('#primary-color').val('#' + hex).trigger('change');
                $('#primary span').css('backgroundColor', '#' + hex);
            }
        });

        $('#primary-color').ColorPicker({
            onChange: function (hsb, hex, rgb) {
                $('#primary-color').val('#' + hex).trigger('change');;
                $('#primary span').css('backgroundColor', '#' + hex);
            },
            onSubmit: function (hsb, hex, rgb, el) {
                $(el).val('#' + hex).trigger('change');
                $(el).closest().find('span.color').css('backgroundColor', '#' + hex);
                $(el).ColorPickerHide();
            },
            onBeforeShow: function () {
                $(this).ColorPickerSetColor(this.value);
                $(this).closest('.form-group').find('.general-color-picker').ColorPickerSetColor(this.value);
                $(this).closest('.form-group').find('.color').css('backgroundColor', this.value);
            }
        })
        .bind('keyup', function () {
            $(this).ColorPickerSetColor(this.value);
            $(this).closest('.form-group').find('.general-color-picker').ColorPickerSetColor(this.value);
            $(this).closest('.form-group').find('.color').css('backgroundColor', this.value);
        });
        
        $('#secondary').ColorPicker({
            onChange: function (hsb, hex, rgb) {
                $('#secondary-color').val('#' + hex).trigger('change');
                $('#secondary span').css('backgroundColor', '#' + hex);
            }
        });

        $('#secondary-color').ColorPicker({
            onChange: function (hsb, hex, rgb) {
                $('#secondary-color').val('#' + hex).trigger('change');
                $('#secondary span').css('backgroundColor', '#' + hex);
            },
            onSubmit: function (hsb, hex, rgb, el) {
                $(el).val('#' + hex).trigger('change');
                $(el).closest().find('span.color').css('backgroundColor', '#' + hex);
                $(el).ColorPickerHide();
            },
            onBeforeShow: function () {
                $(this).ColorPickerSetColor(this.value);
                $(this).closest('.form-group').find('.general-color-picker').ColorPickerSetColor(this.value);
                $(this).closest('.form-group').find('.color').css('backgroundColor', this.value);
            }
        })
        .bind('keyup', function () {
            $(this).ColorPickerSetColor(this.value);
            $(this).closest('.form-group').find('.general-color-picker').ColorPickerSetColor(this.value);
            $(this).closest('.form-group').find('.color').css('backgroundColor', this.value);
        });
    },
    
    addMoreDelivery: function(){
        $(document).on('click', '.ir-add-more', function(){
            var defaultDeliveryRow = $('.delivery-default-row');
            var body = $('.update-form-delivery-conditions .list-body');
            var lastRow = body.find('.list-item-none').last();
            var lastNumber = lastRow.data('number');
            var newNumber = lastNumber + 1;
            var newRow = $('<div class="list-item-none row" data-number="'+ newNumber +'"></div>');

            newRow.append(defaultDeliveryRow.html());
            newRow.find('[name="id"]').attr('name', 'delivery['+ newNumber +'][id]');
            newRow.find('[name="area_start"]').attr('name', 'delivery['+ newNumber +'][area_start]');
            newRow.find('[name="area_end"]').attr('name', 'delivery['+ newNumber +'][area_end]');
            newRow.find('[name="price_min"]').attr('name', 'delivery['+ newNumber +'][price_min]');
            newRow.find('[name="price"]').attr('name', 'delivery['+ newNumber +'][price]');
            newRow.find('[name="free"]').attr('name', 'delivery['+ newNumber +'][free]');
            newRow.find('.row-number').text((parseInt(newNumber) + 1)+'.');
            newRow.find('.slider-range-temp').attr('class', 'slider-range range-small pull-left');
            body.append(newRow);
            
            Notification.fn.rangeSlider();
        });
    },
    
    deleteDelivery: function(){
        $(document).on('click', '.remove-delivery, .remove-print', function() {
            var form = $(this).closest('form'); 
            setTimeout(function () {
                form.submit();
            }, 1000);
            
            $(this).closest('.list-item-none').remove();
        });
    },
    
    addMorePrint: function(){
        $(document).on('click', '.btn-print-add', function(){
            var id = $(this).data('id');
            var field = $(this).data('field');
            var type = $(this).data('type');
            
            var defaultPrintRow = $('.'+field+'-default-row');
            var body = $(id+' .list-body');
            var lastRow = body.find('.list-item-none').last();
            var lastNumber = lastRow.data('number');
            var newNumber = lastNumber + 1;
            var newRow = $('<div class="full-width pull-left list-item-none" data-number="'+ newNumber +'"></div>');

            newRow.append(defaultPrintRow.html());
            newRow.find('[name="id"]').attr('name', field+'['+ newNumber +'][id]');
            newRow.find('[name="type"]').attr('name', field+'['+ newNumber +'][type]');
            newRow.find('[name="mac"]').attr('name', field+'['+ newNumber +'][mac]');
            newRow.find('[name="copy"]').attr('name', field+'['+ newNumber +'][copy]');
            newRow.find('[name="auto"]').attr('name', field+'['+ newNumber +'][auto]');
            newRow.find('#print-auto').attr('id', field+'-'+ type +'-'+ newNumber +'-auto');
            newRow.find('[for="print-auto"]').attr('for', field+'-'+ type +'-'+ newNumber +'-auto');
            newRow.find('.row-number').text((parseInt(newNumber) + 1)+'.');
            body.append(newRow);
        });
    },

    rule: function () {
        $(document).ready(function () {
            Setting.fn.init.call(this);
        });
    },
};

Setting.fn.rule();