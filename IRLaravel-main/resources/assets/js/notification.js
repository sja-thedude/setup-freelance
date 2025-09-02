function Notification() {
}

Notification.fn = {
    init: function () {
        Notification.fn.submitCreateForm.call(this);
        Notification.fn.rangeSlider.call(this);
        Notification.fn.notificationSendTime.call(this);
        Notification.fn.notificationSendToEveryone.call(this);
    },

    submitCreateForm: function () {
        $('.create-notification').map(function () {
            $(this).validate({
                // onkeyup: function (element) {
                //     $(element).valid();
                // },
                onkeyup: false,
                onfocusout: false,
                rules: {
                    description: {
                        customRequired: true
                    },
                    location_radius: {
                        onlyFullNumber: true
                    },
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
                submitHandler: function (form) {
                    if ($(form).valid()) {
                        $('body').loading('toggle');
                        var elem = $(form);

                        $.ajax({
                            url: elem.attr('action'),
                            type: elem.attr('method'),
                            data: elem.serialize(),
                            beforeSend: function () {
                                elem.addClass('disabled');
                                elem.find('.save-form').attr('disabled', 'disabled');
                            },
                            success: function (response) {
                                $('body').loading('toggle');
                                var txtMessage = response.message;

                                $('.ir-modal').modal('hide');

                                if (response.success == true) {
                                    Swal.fire({
                                        title: '<span class="ir-h3">' + txtMessage + '</span>',
                                        html: '<span class="ir-popup-content mgt-40"><img src="/assets/images/sent.svg" /></span>',
                                        width: 600,
                                        padding: '58px 35px 75px 35px',
                                        showConfirmButton: false,
                                        showCloseButton: true,
                                        showCancelButton: false,
                                        cancelButtonText: Lang.get('workspace.close')
                                    }).then((result) => {
                                        // $('body').loading('toggle');
                                        // elem[0].reset();
                                        //
                                        // //Reset select2
                                        // if (elem.find('.select2').length || elem.find('.select2-tag').length || elem.find('.select2-type').length) {
                                        //     elem.find('.select2, .select2-tag, .select2-type').val('').trigger('change');
                                        // }

                                        location.reload();
                                    });
                                } else {
                                    if (response.data) {
                                        MainShared.fn.showErrorMessages(response, elem);
                                    }
                                }
                            }
                        }).error(function (XMLHttpRequest, textStatus, errorThrown) {
                            var response = XMLHttpRequest.responseJSON;

                            if (response.data) {
                                MainShared.fn.showErrorMessages(response, elem);
                            }
                        }).always(function () {
                            elem.removeClass('disabled');
                            elem.find('.save-form').removeAttr('disabled');
                            $('body').loading('toggle');
                        });
                    }
                },
            });
        });
    },

    rangeSlider: function () {
        if ($('.slider-range').length) {
            $('.slider-range').map(function () {
                var _this = $(this);
                var start_age_dest = _this.closest('.wrap-slider-range').find('.start_age_dest');
                var end_age_dest = _this.closest('.wrap-slider-range').find('.end_age_dest');

                _this.slider({
                    range: true,
                    min: 0,
                    max: 200,
                    values: [start_age_dest.val(), end_age_dest.val()],
                    slide: function (event, ui) {
                        var min = ui.values[0];
                        var max = ui.values[1];
                        _this.find('.ui-slider-range').next('span.ui-slider-handle').html('<span class="ui-slider-handle-min">' + min + '</span>');
                        _this.find('.ui-slider-range').next().next('span.ui-slider-handle').html('<span class="ui-slider-handle-max">' + max + '</span>');

                        start_age_dest.val(min);
                        end_age_dest.val(max);
                    },
                    change: function (event, ui) {
                        if (event.originalEvent) {
                            setTimeout(function () {
                                if (_this.closest('form').attr('class') == 'update-form-delivery-conditions') {
                                    _this.closest('form').submit();
                                }
                            }, 1000);
                        }
                    }
                });

                var min = _this.slider("values", 0);
                var max = _this.slider("values", 1);

                _this.find('.ui-slider-range').next('span.ui-slider-handle').html('<span class="ui-slider-handle-max">' + min + '</span>');
                _this.find('.ui-slider-range').next().next('span.ui-slider-handle').html('<span class="ui-slider-handle-max">' + max + '</span>');

                start_age_dest.val(min);
                end_age_dest.val(max);
            });
        }
    },

    notificationSendTime: function () {
        $('input[name=send_now]').on('ifChecked', function (event) {
            Notification.fn.checkAndDisableWhenChange($(this));
        });
    },

    notificationSendToEveryone: function () {
        $('input[name=is_send_everyone]').on('ifChecked', function (event) {
            Notification.fn.checkAndDisableWhenChange($(this));
        });
    },

    checkAndDisableWhenChange: function (selector) {
        if (selector.val() === '1') {
            selector.closest('.form-group').find('.noti-group input').prop('disabled', true);
            selector.closest('.form-group').find('.noti-group').addClass('disable');
        }
        if (selector.val() === '0') {
            selector.closest('.form-group').find('.noti-group input').prop('disabled', false);
            selector.closest('.form-group').find('.noti-group').removeClass('disable');
        }

        selector.closest('.form-group').find('.noti-group span.ir-btn-date-range').toggleClass('date-range-trigger');
        selector.closest('.form-group').find('.maps-marker').toggleClass('event-default');
    },

    rule: function () {
        $(document).ready(function () {
            Notification.fn.init.call(this);
        });
    },
};

Notification.fn.rule();
