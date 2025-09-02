function MainShared() {
}

MainShared.fn = {
    init: function () {
        MainShared.fn.convertNumber.call(this);
        MainShared.fn.updateStatus.call(this);
        MainShared.fn.onKeypressSearch.call(this);
        MainShared.fn.showEditForm.call(this);
        MainShared.fn.closeEditForm.call(this);
        MainShared.fn.submitCreateForm.call(this);
        MainShared.fn.submitUpdateForm.call(this);
        MainShared.fn.readUrlImage.call(this);
        MainShared.fn.initSelect2Tags.call(this);
        MainShared.fn.select2Type.call(this);
        MainShared.fn.hideShowArea.call(this);
        MainShared.fn.resetForm.call(this);
        MainShared.fn.customRequired.call(this);
        MainShared.fn.integerFormat.call(this);
        MainShared.fn.timeFormat.call(this);
        MainShared.fn.onlyText.call(this);
        MainShared.fn.onlyNumber.call(this);
        MainShared.fn.onlyFullNumber.call(this);
        MainShared.fn.phoneValid.call(this);
        MainShared.fn.emailValid.call(this);
        MainShared.fn.customValidateCutoffTime.call(this);
        MainShared.fn.checkSubmitButton.call(this);
        MainShared.fn.validateMin.call(this);
        MainShared.fn.validateMax.call(this);
        MainShared.fn.resetWhenCloseModal.call(this);
        MainShared.fn.hideWhenClick.call(this);
        MainShared.fn.autoSubmit.call(this);
        MainShared.fn.convertUtcToLocalTime.call(this);
        MainShared.fn.convertUtcToLocalTimeOrder.call(this);
        MainShared.fn.autoDetectTimeZone.call(this);
        MainShared.fn.expand.call(this);
        MainShared.fn.datePicker.call(this);
        MainShared.fn.checkChangeForm.call(this);
        // MainShared.fn.triggerDateRange.call(this);
        MainShared.fn.autoFillSlug.call(this);
        MainShared.fn.disableSubmitIfFieldNotFilled.call(this);
        MainShared.fn.keyupGSM.call(this);
        MainShared.fn.initDropzone.call(this);
        MainShared.fn.switchToggleDiv.call(this);
    },

    convertNumber: function () {
        var handleNumberChange = function (element) {
            var val = $(element).val();
            var type = $(element).attr('type');
            var min = $(element).attr('min');

            if(typeof type != 'undefined' && type == 'number') {
                $(element).attr('type', 'text');
                $(element).removeClass('is-number').addClass('is-number');
            }

            if(val != '') {
                val = val.replace(/,/g, '.');

                if(typeof min != 'undefined' && parseFloat(val) < parseFloat(min)){
                    val = min;
                }

                $(element).val(val);
            }
        };

        $('.is-number').map(function () {
            handleNumberChange(this);
        });

        $(document).on('input', '.is-number', function (event) {
            var input = event.target.value;

            // Replace anything other than digits, comma, or dot
            input = input.replace(/(?!^-)[^0-9.,]/g, '');
            // Ensure only the first comma or dot is kept, remove any others
            var firstOccurrence = input.match(/[.,]/); // Find the first comma or dot
            if (firstOccurrence) {
                var symbol = firstOccurrence[0];
                // Allow only the first comma or dot, remove others
                input = input.split(symbol)[0] + symbol + input.split(symbol)[1].replace(/[.,]/g, '');
            }

            event.target.value = input;
        });

        $(document).on('keyup', '.is-number', function () {
            handleNumberChange(this);
        });

        $('input[type="number"]').map(function () {
            handleNumberChange(this);
        });

        $('.modal').on('show.bs.modal', function () {
            $('input[type="number"]').map(function () {
                handleNumberChange(this);
            });
        });
    },

    customValidateCutoffTime: function() {
        $.validator.addMethod( "cutoffTimeValidate", function( value, element ) {
            var target = $(element).attr('target_element');
            var targetValue = $(target).val();
            var startTime = moment(targetValue, 'HH:mm');
            var endTime = moment(value, 'HH:mm');
            return startTime.isBefore(endTime);
        }, Lang.get('common.validation.pickup_delivery_time_greater_cut_off'));

        $.validator.addMethod( "cutoffTimeRequired", function( value, element ) {
            return value != '00:00';
        }, Lang.get('common.validation.field_required'));
    },

    disableSubmitIfFieldNotFilled: function () {
        if ($('.validate-submit').length) {
            var loadValidate = function (form) {
                var flag = true;

                form.find('.need-required').map(function () {
                    if (($(this).val()).trim() == '') {
                        flag = false;
                    }
                });

                if (flag == true) {
                    form.find('.validate-submit').removeAttr('disabled');
                } else {
                    form.find('.validate-submit').attr('disabled', true);
                }
            };

            loadValidate($('.required-all-field'));

            $(document).on('keyup change click', '.need-required', function () {
                loadValidate($(this).closest('.required-all-field'));
            });
        }
    },

    autoFillSlug: function () {
        $(document).on('change', '.fill-slug', function () {
            var text = $(this).val();
            $(this).val(MainShared.fn.convertToSlug(text));
        });
    },

    convertToSlug: function (str) {
        // Chuyển hết sang chữ thường
        str = str.toLowerCase();

        // xóa dấu
        str = str.replace(/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/g, 'a');
        str = str.replace(/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/g, 'e');
        str = str.replace(/(ì|í|ị|ỉ|ĩ)/g, 'i');
        str = str.replace(/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/g, 'o');
        str = str.replace(/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/g, 'u');
        str = str.replace(/(ỳ|ý|ỵ|ỷ|ỹ)/g, 'y');
        str = str.replace(/(đ)/g, 'd');

        // Xóa ký tự đặc biệt
        str = str.replace(/([^0-9a-z-\s])/g, '');

        // Xóa khoảng trắng thay bằng ký tự -
        str = str.replace(/(\s+)/g, '-');

        // xóa phần dự - ở đầu
        str = str.replace(/^-+/g, '');

        // xóa phần dư - ở cuối
        str = str.replace(/-+$/g, '');

        // return
        return str;
    },

    triggerDateRange: function () {
        $(document).on('click', '.ir-btn-date-range', function () {
            var input = $(this).closest('.ir-group-date-range').find('.ir-input');

            if (input.length) {
                input.trigger('click');
            }
        });
    },

    formatNumberDecimal: function () {
        $('.auto-format-decimal').map(function () {
            $(this).val((Math.round($(this).val() * 100) / 100).toFixed(2));
        });
    },

    checkChangeForm: function () {
        if ($('.check-change').length) {
            $(document).on('click change keyup', '.check-change', function () {
                var form = $(this).closest('form');
                var flag = false;

                form.find('.check-change').map(function () {
                    if ($(this).val() != $(this).data('origin')) {
                        flag = true;
                    }
                });

                if (flag == true) {
                    form.find('.submit-check').removeAttr('disabled');
                } else {
                    form.find('.submit-check').attr('disabled', 'disabled');
                }
            });
        }
    },

    setAfterChanged: function () {
        if ($('.check-change').length) {
            var form = $('.check-change').closest('form');

            form.find('.check-change').map(function () {
                var val = $(this).val();
                $(this).attr('data-origin', val);
            });

            form.find('.submit-check').attr('disabled', 'disabled');
        }
    },

    expand: function () {
        $(document).on('click', '.has-expand', function () {
            var row = $(this).closest('.root-expand');
            var dest = row.find('.dest-expand');

            dest.toggle();
        });
    },

    delay: function (callback, ms) {
        var timer = 0;
        return function () {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    },

    convertUtcToLocalTime: function () {
        if ($('.time-convert').length) {
            $('.time-convert').map(function () {
                var format = $(this).data('format');
                var utcTime = $(this).data('datetime');
                var localTime = moment.utc(utcTime).local().format(format);

                $(this).empty().text(localTime);
            });
        }
    },

    convertUtcToLocalTimeOrder: function () {
        if ($('.time-convert.order').length) {
            $('.time-convert.order').map(function () {
                var showtime = $(this).data('showtime');
                var dateFormat = $(this).data('format');
                var timeFormat = $(this).data('timeformat');
                var utcTime = $(this).data('datetime');
                var localDate = dateFormat ? moment.utc(utcTime).local().format(dateFormat) : '';
                var localTime = timeFormat ? moment.utc(utcTime).local().format(timeFormat) : '';

                $(this).empty().html('<span class="'+showtime+'">'+localDate+'</span><span class="font-size-21 mgl-5" style="font-weight: bold;position:relative;top:1px;">'+localTime+'</span>');
            });
        }
    },

    autoDetectTimeZone: function () {
        $('.auto-detect-timezone').map(function () {
            $(this).val(moment.tz.guess());
        });
    },

    autoSubmit: function () {
        var autoSubmitProcess = function (_this) {
            var type = $(_this).data('type');

            if (type == 'vat') {
                VAT.fn.autoSubmitVat.call(_this);
            } else if (type == 'setting_open_hour_active') {
                SettingOpenHour.fn.formOpenHourChangeActive.call(_this);
            } else if (type == 'setting_time_slot_detail') {
                SettingTimeSlot.fn.autoSubmitTimeSlotDetail.call(_this);
            } else if (type == 'setting_time_slot') {
                SettingTimeSlot.fn.autoSubmit.call(_this);
            } else if (
                type == 'general_profile' ||
                type == 'general_workspace' ||
                type == 'general_settings' ||
                type == 'payment_methods' ||
                type == 'preferences' ||
                type == 'delivery' ||
                type == 'print'
            ) {
                Setting.fn.autoSubmitGeneral.call(_this);
            }
        };

        $(document).on('keyup', 'input.auto-submit', MainShared.fn.delay(function (event) {
            autoSubmitProcess(this);
        }, 1000));
        $(document).on('change', 'input.auto-submit-now', function () {
            autoSubmitProcess(this);
        });
        $(document).on('keyup', 'textarea.auto-submit', MainShared.fn.delay(function (event) {
            autoSubmitProcess(this);
        }, 1000));
        $(document).on('change', 'select.auto-submit', MainShared.fn.delay(function (event) {
            autoSubmitProcess(this);
        }, 100));
        $(document).on('change', 'input[type="checkbox"].auto-submit', MainShared.fn.delay(function (event) {
            autoSubmitProcess(this);
        }, 100));
        $(document).on('change', 'input[type="radio"].auto-submit', MainShared.fn.delay(function (event) {
            autoSubmitProcess(this);
        }, 100));

        //Others case for setting general
        $(document).on('change', 'input[type="file"].auto-submit, ' +
            'input.location.auto-submit, ' +
            'input.latitude.auto-submit, ' +
            'input.longitude.auto-submit, ' +
            'input.primary_color.auto-submit, ' +
            'input.second_color.auto-submit', MainShared.fn.delay(function (event) {
            autoSubmitProcess(this);
        }, 100));
    },

    hideWhenClick: function () {
        $(document).on('click', '.hide-when-click', function () {
            $(this).hide();
        });
    },

    resetWhenCloseModal: function () {
        $('.modal').on('hide.bs.modal', function (event) {
            var form = $(this).find('form.form-reset-close');

            if (form.length) {
                if (!form.hasClass('only-reset-validate')) {
                    form[0].reset();
                }

                var validator = form.validate();
                validator.resetForm();
                form.find('[aria-invalid="true"]').removeClass('error').attr('aria-invalid', false);
            }
        });
    },

    validateMin: function () {
        $(document).on('change keyup', '.validate-min', function () {
            var value = $(this).val();
            var min = $(this).data('min');

            if (value < min) {
                $(this).val(min);
            }
        });
    },

    validateMax: function () {
        $(document).on('change keyup', '.validate-max', function () {
            var value = $(this).val();
            var max = $(this).data('max');

            if (value > max) {
                $(this).val(max);
            }
        });
    },

    customRequired: function () {
        $.validator.addMethod("required", function (value, element) {
            if ($.trim(value).length === 0) {
                $(element).val('');
                return false;
            }

            return true;
        }, Lang.get('common.validation.field_required'));

        $.validator.addMethod("customRequired", function (value, element) {
            if ($.trim(value).length === 0) {
                $(element).val('');
                return false;
            }

            return true;
        }, Lang.get('common.validation.field_required'));
    },

    timeFormat: function () {
        $.validator.addMethod("timeFormat", function (value, element) {
            return this.optional(element) || /^([01]\d|2[0-3]):([0-5]\d)$/.test(value);
        }, Lang.get('common.validation.time_format'));
    },


    integerFormat: function () {
        $.validator.addMethod("integerFormat", function (value, element) {
            return this.optional(element) || (Math.floor(value) == value);
        }, Lang.get('common.validation.time_format'));
    },

    onlyText: function () {
        $.validator.addMethod("onlyText", function (value, element) {
            return this.optional(element) || !/\d+$/.test(value);
        }, Lang.get('common.validation.only_text'));
    },

    phoneValid: function () {
        $.validator.addMethod("phoneValidate", function (value, element) {
            return this.optional(element) || /^\+{1}[0-9|\/]{10,19}$/.test(value);
        }, Lang.get('common.validation.phone_valid'));
    },

    emailValid: function () {
        $.validator.addMethod("email", function (value, element) {
            return this.optional(element) || /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value);
        }, Lang.get('common.validation.email_valid'));

        $.validator.addMethod("emailValidate", function (value, element) {
            return this.optional(element) || /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value);
        }, Lang.get('common.validation.email_valid'));
    },

    onlyNumber: function () {
        $.validator.addMethod("onlyNumber", function (value, element) {
            return this.optional(element) || /^[1-9]\d*$/.test(value);
        }, Lang.get('common.validation.number_valid'));
    },

    onlyFullNumber: function () {
        $.validator.addMethod("onlyFullNumber", function (value, element) {
            return this.optional(element) || /^[0-9]\d*$/.test(value);
        }, Lang.get('common.validation.number_valid'));
    },

    resetForm: function () {
        $(document).on('click', '.reset-form', function () {
            var form = $(this).closest('form');

            if (form.length) {
                form[0].reset();

                //Reset select2
                if (form.attr('id') == 'create-form' || form.attr('class') == 'create-notification' || $(".another-account-manager".length)) {
                    if (form.find('.select2').length || form.find('.select2-tag').length || form.find('.select2-type').length) {
                        form.find('.select2, .select2-tag, .select2-type').val('').trigger('change');
                    }
                }

                //Reset other for restaurants | notification
                if (form.attr('id') == 'create-form' || form.attr('id') == 'workspace-assign-manager' || form.attr('class') == 'create-notification') {
                    //Reset avatar to default
                    if (form.find('.show-image').length) {
                        form.find('.show-image').attr('src', '/assets/images/no-image.svg');
                    }
                } else if (form.attr('class') == 'update-form') {
                    //Reset avatar to default
                    var imgageDefault = form.closest('.modal-body').find('.form-detail .image-detail').attr('src');
                    form.find('.show-image').attr('src', imgageDefault);
                }

                var validator = form.validate();
                validator.resetForm();
                form.find('[aria-invalid="true"]').removeClass('error').attr('aria-invalid', false);
                form.find('.form-control, .select2').removeClass('error').attr('aria-invalid', false);

                //Reset range slide
                if (form.attr('class') == 'create-notification') {
                    location.reload();
                }
            }
        });
    },

    updateStatus: function () {
        $(document).on('click', '.update-status', function () {
            $('body').loading('toggle');
            var _this = $(this);

            $.ajax({
                type: 'POST',
                url: _this.data('route'),
                data: {
                    "_token": $("input[name='_token']").val(),
                    "status": _this.prev('.switch-input').val(),
                }
            })
                .done(function (respon) {
                    $('body').loading('toggle');

                    //Using only restaurant module
                    if (typeof respon.data != "undefined" && $(".switch-" + respon.data.id).length) {
                        $(".switch-" + respon.data.id).find('.switch-input').val(respon.status == false ? 1 : 0);
                        $('.switch-' + respon.data.id + ' .switch-input').map(function () {
                            if (respon.data.active == true) {
                                if (!$(this).is(':checked')) {
                                    $(this).trigger('click');
                                }
                            } else {
                                if ($(this).is(':checked')) {
                                    $(this).trigger('click');
                                }
                            }
                        });
                    } else {
                        _this.parent().find('.switch-input').val(respon.status == false ? 1 : 0);
                    }
                });
        });
    },

    onKeypressSearch: function () {
        $(document).on('keyup', '.keypress-search', MainShared.fn.delay(function (event) {
            if ($(this).find('.ir-input').val().length > 2 || $(this).find('.ir-input').val().length == 0) {
                $(this).submit();
            }
        }, 1000));
    },

    showEditForm: function () {
        $(document).on('click', '.show-edit-form', function () {
            $(".form-detail").hide();
            $(".form-edit").fadeIn();

            $('.select2, .select2-tag, .select2-type').trigger('change');

            var form = $(this).closest('form');

            if (form.length) {
                form[0].reset();

                var validator = form.validate();
                validator.resetForm();
                form.find('[aria-invalid="true"]').removeClass('error').attr('aria-invalid', false);
                form.find('.form-control, .select2').removeClass('error').attr('aria-invalid', false);
            }
        });

        $(document).on('click', '.show-detail-form', function () {
            $(".form-edit").hide();
            $(".form-detail").fadeIn();

            $('.select2, .select2-tag, .select2-type').trigger('change');

            var form = $(this).closest('form');

            if (form.length) {
                form[0].reset();

                var validator = form.validate();
                validator.resetForm();
                form.find('[aria-invalid="true"]').removeClass('error').attr('aria-invalid', false);
                form.find('.form-control, .select2').removeClass('error').attr('aria-invalid', false);
            }
        });
    },

    closeEditForm: function () {
        $(document).on('click', '[data-dismiss="modal"]', function () {
            // $('.ir-modal').modal('hide');

            $(".form-edit").hide();
            $(".form-detail").show();

            var form = $(this).closest('.modal-content').find('form');

            if (form.length) {
                form[0].reset();
                var validator = form.validate();
                validator.resetForm();
                form.find('[aria-invalid="true"]').removeClass('error').attr('aria-invalid', false);
            }
        });
    },

    submitCreateForm: function () {
        $('#create-form').validate({
            onkeyup: false,
            onfocusout: false,
            errorPlacement: function (error, element) {
                var elem = $(element);
                if (elem.hasClass('select2') || elem.hasClass('select2-tag')) {
                    error.hide();
                    error.insertAfter(elem.closest('.form-group').find('.select2'));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element, errorClass, validClass) {
                var elem = $(element);
                if (elem.hasClass('select2') || elem.hasClass('select2-tag')) {
                    elem.closest('.form-group').find('.select2').addClass(errorClass);
                } else {
                    elem.addClass(errorClass);
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                var elem = $(element);
                if (elem.hasClass('select2') || elem.hasClass('select2-tag')) {
                    elem.closest('.form-group').find('.select2').removeClass(errorClass);
                } else {
                    elem.removeClass(errorClass);
                }
            },
            rules: {
                name: {
                    customRequired: true
                },
                slug: {
                    customRequired: true
                },
                account_manager_id: {
                    customRequired: true,
                },
                gsm: {
                    customRequired: true,
                    phoneValidate: true
                },
                manager_name: {
                    customRequired: true
                },
                surname: {
                    customRequired: true
                },
                email: {
                    customRequired: true,
                    emailValidate: true
                },
                email_to: {
                    customRequired: true,
                    emailValidate: true
                }
            },
            submitHandler: function (form) {
                MainShared.fn.processFormByAjax(form, 'create');
            },
        });
    },

    submitUpdateForm: function () {
        $('.update-form').map(function () {
            $(this).validate({
                onkeyup: false,
                onfocusout: false,
                // errorPlacement: function (error, element) {
                //     var elem = $(element);
                //     if (elem.hasClass('select2') || elem.hasClass('select2-tag')) {
                //         error.hide();
                //         error.insertAfter(elem.closest('.form-group').find('.select2'));
                //     } else {
                //         error.insertAfter(element);
                //     }
                // },
                // highlight: function (element, errorClass, validClass) {
                //     var elem = $(element);
                //     if (elem.hasClass('select2') || elem.hasClass('select2-tag')) {
                //         elem.closest('.form-group').find('.select2').addClass(errorClass);
                //     } else {
                //         elem.addClass(errorClass);
                //     }
                // },
                // unhighlight: function (element, errorClass, validClass) {
                //     var elem = $(element);
                //     if (elem.hasClass('select2') || elem.hasClass('select2-tag')) {
                //         elem.closest('.form-group').find('.select2').removeClass(errorClass);
                //     } else {
                //         elem.removeClass(errorClass);
                //     }
                // },
                rules: {
                    name: {
                        customRequired: true
                    },
                    slug: {
                        customRequired: true
                    },
                    account_manager_id: {
                        customRequired: true,
                    },
                    gsm: {
                        customRequired: true,
                        phoneValidate: true
                    },
                    manager_name: {
                        customRequired: true
                    },
                    surname: {
                        customRequired: true
                    },
                    email: {
                        customRequired: true,
                        emailValidate: true
                    },
                    email_to: {
                        customRequired: true,
                        emailValidate: true
                    }
                },
                submitHandler: function (form) {
                    MainShared.fn.processFormByAjax(form, 'update');
                },
            });
        });
    },

    readUrlImage: function () {
        $(".manager-upload-image").change(function () {
            var input = this;
            if (input.files && input.files[0]) {
                var fileType = this.files[0]['type'];
                const validImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                if (validImageTypes.includes(fileType)) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $(input).prev('.show-image').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]);
                } else {
                    Swal.fire({
                        title: Lang.get('workspace.upload_image_mimes'),
                        type: "error",
                    });
                }
            }
        });
    },

    showErrorMessages: function (response, selfForm, isArrayError) {
        selfForm.find('.form-control, .select2, .select2-tags, .selectpicker').each(function (k, v) {
            $(v).removeClass('error');
        });
        $('#selectFeaturedProducts').css('border', '1px solid #ccc');
        $('#category_ids').css('border', '1px solid #ccc');

        $.each(response.data, function (index, message) {
            if (typeof isArrayError == 'undefined') {
                var ipName = '[name="' + index + '"]';
                var lblName = '#' + index + '-error';
            } else {
                var split = index.split('.');
                var key = '';

                split.forEach(function (element, i) {
                    if (i == 0) {
                        key += element;
                    } else {
                        key += '[' + element + ']';
                    }
                });

                var ipName = '[name="' + key + '"]';
                var lblName = '#' + key + '-error';
            }

            var input = selfForm.find(ipName);
            var inputError = selfForm.find(lblName);

            if (index === "days" && (selfForm.attr('id') === "create_product" || selfForm.attr('id') === "update_product")) {
                selfForm.find('.days').append('<label id="' + index + '-error" class="error" for="' + index + '">' + message + '</label>')
            }

            if (index == 'types') {
                $('#' + index).closest('.form-group').find('.select2').addClass('error');
                $('<label id="types-error" class="error" style="" for="types">' + message + '</label>').insertAfter($('#' + index).closest('.form-group').find('.select2'));
            }

            if (input.length) {
                input.removeClass('error').addClass('error').attr('aria-invalid', true);

                if (inputError.length) {
                    inputError.empty().text(message).show();
                } else {
                    input.after('<label id="' + index + '-error" class="error" for="' + index + '">' + message + '</label>');
                }
            }

            if (index == 'category' || index == 'category_ids' || index == 'products') {
                $('#selectFeaturedProducts').closest('.form-group').find('.select2-selection__rendered').css('border', '1px solid red');

                // $('<label class="error">' + message + '</label>').insertAfter($('#category_ids').closest('.form-group').find('.bootstrap-select'));
                $('<label class="error">' + message + '</label>').insertAfter($('#selectFeaturedProducts').closest('.form-group').find('.select2'));
                $('#category_ids').closest('.form-group').find('.dropdown-toggle').css('border', '1px solid red');
            } else {
                $('#selectFeaturedProducts').closest('.form-group').find('.select2-selection__rendered').css('border', '1px solid #ccc');
                $('#category_ids').closest('.form-group').find('.dropdown-toggle').css('border', '1px solid #ccc');
            }
        });
    },

    showToastError: function (response) {
        var messages = '';

        $.each(response.data, function (index, message) {
            messages += message + ' ';
        });

        toastr.error(messages);
    },

    processFormByAjax: function (form, type) {
        if ($(form).valid()) {
            $('body').loading('toggle');
            var elem = $(form);
            var formData = new FormData(elem[0]);

            $.ajax({
                url: elem.attr('action'),
                type: elem.attr('method'),
                // data: elem.serialize(),
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    elem.addClass('disabled');
                    elem.find('.save-form').attr('disabled', 'disabled');
                },
                success: function (response) {
                    // $('body').loading('toggle');
                    var txtMessage = type === "create"
                        ? Lang.get('workspace.created_successfully')
                        : Lang.get('workspace.updated_successfully');

                    if (type === "update") {
                        $('#detail-' + response.data.id).modal('hide');
                    } else {
                        $('#form-create').modal('hide');
                    }

                    if (response.success == true) {
                        if (type === "create") {
                            Swal.fire({
                                title: '<span class="ir-popup-title">' + txtMessage + '</span>',
                                html: '<span class="ir-popup-content">' + response.message + '</span>',
                                width: 512,
                                padding: '43px 60px 30px 60px',
                                showConfirmButton: false,
                                showCloseButton: true,
                                showCancelButton: true,
                                cancelButtonText: Lang.get('workspace.close')
                            }).then((result) => {
                                location.reload();
                            });
                        } else {
                            location.reload();
                        }
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

    initSelect2Tags: function () {
        var selector = $('.select2-tag');

        $(selector).select2({
            placeholder: Lang.get('workspace.placeholder_select_type'),
            tags: true,
            createTag: function (tag) {
                return {
                    id: tag.term,
                    text: tag.term,
                    tag: true
                };
            }
        }).on("change", function (e) {
            var isNew = $(this).find('[data-select2-tag="true"]');
            if (isNew.length && $.inArray(isNew.val(), $(this).val()) !== -1) {

                //Ajax create the new tag
                $.ajax({
                    url: $(this).data('route'),
                    type: "post",
                    data: {name: isNew.val()},
                    beforeSend: function () {
                    },
                    success: function (response) {
                        if (response.success == true) {
                            isNew.replaceWith('<option selected value="' + response.data.id + '">' + response.data.name + '</option>');
                        } else {
                            if (response.data) {
                                MainShared.fn.showErrorMessages(response, selector);
                            }
                        }
                    }
                });

                return false;
            }
        });
    },

    select2Type: function () {
        $(".select2-type").select2({
            placeholder: Lang.get('workspace.placeholder_select_type')
        });
    },

    hideShowArea: function () {
        $(document).on('click', '.show-hide-actions', function () {
            var _this = $(this);
            var target = _this.data('target');
            var showHideArea = $('.show-hide-area');
            var activeArea = $('.show-hide-area[data-id="' + target + '"]');
            showHideArea.attr('disabled', 'disabled');
            showHideArea.hide();
            activeArea.removeAttr('disabled');
            activeArea.show();
        });
    },

    checkSubmitButton: function () {
        $('.submit').map(function () {
            $(this).attr('disabled', 'disabled');
        });

        $(document).on('change keyup', 'form input', function () {
            var submit = $(this).closest('form').find('.submit');
            MainShared.fn.triggerValidateSubmitButton(submit);
        });
        $(document).on('change', 'form select', function () {
            var submit = $(this).closest('form').find('.submit');
            MainShared.fn.triggerValidateSubmitButton(submit);
        });
        $(document).on('change keyup', 'form textarea', function () {
            var submit = $(this).closest('form').find('.submit');
            MainShared.fn.triggerValidateSubmitButton(submit);
        });
    },

    triggerValidateSubmitButton: function (submit) {
        if (submit.length) {
            var form = submit.closest('form');

            if (form.length) {
                if (form.valid()) {
                    submit.removeAttr('disabled');
                } else {
                    submit.attr('disabled', 'disabled');
                }
            }
        }
    },

    datePicker: function () {
        var startDate = moment().startOf('day');
        var endDate = moment().startOf('day').add(23, 'hour').add(59, 'minute').add(59, 'second');
        var dateRange = $('.ir-group-datepicker');
        var objDatepicker = $('.ir-group-datepicker .ir-datepicker');
        var position = dateRange.find('.start_date').data('position');
        var minDate = dateRange.data('min-date');
        var formatDateTimeNormal = 'DD/MM/YYYY HH:mm'
        var formatDateTimeSpecial = 'MM/DD/YYYY HH:mm';
        let drpOptions = {
            // drops: 'auto',
            minDate: minDate ? false : new Date(),
            singleDatePicker: true,
            timePicker: true,
            drops: position,
            timePicker24Hour: true,
            autoUpdateInput: true,
            startDate: startDate,
            //parentEl :'.modal',
            showDropdowns: false,
            endDate: endDate,
            locale: {
                firstDay: 1,
                format: formatDateTimeNormal,
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

        $('.ir-datepicker').daterangepicker(drpOptions, function (start, end, label) {
            MainShared.fn.setDefautDateInput(start);
        });

        $('.ir-datepicker').attr('autocomplete', 'off');

        if (dateRange.length) {
            var startDateOrigin = dateRange.find('.start_date').val();
            var objDaterangepicker = objDatepicker.data('daterangepicker');

            if (startDateOrigin !== '') {

                if (dateRange.find('.start_date').data('timezone')) {
                    startDateOrigin = moment.utc(startDateOrigin).local().format(formatDateTimeSpecial)
                }

                objDaterangepicker.setStartDate(moment(startDateOrigin));

                if (dateRange.find('.start_date').data('single-date')) {
                    objDaterangepicker.setEndDate(moment(startDateOrigin));
                }
            } else {
                objDatepicker.val("");
                MainShared.fn.setDefautDateInput(startDate);
            }
        }

        objDatepicker.on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format(formatDateTimeNormal));
            MainShared.fn.setDefautDateInput(picker.startDate);
        });

        objDatepicker.on('cancel.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format(formatDateTimeNormal));
            MainShared.fn.setDefautDateInput(picker.startDate);
        });

        $(document).on('click', '.date-range-trigger', function () {
            var input = $(this).closest('.ir-group-date-range').find('.ir-input');

            if (input.length) {
                input.trigger('click');
            }
        });
    },

    setDefautDateInput: function (startDate) {
        var dateRange = $('.ir-group-datepicker');

        if (dateRange.length) {
            dateRange.find('.start_date').val(startDate.format('YYYY-MM-DD HH:mm:ss'));
        }
    },

    keyupGSM: function () {
        $(document).on('keypress keyup', '.keyup-gsm', function(){
            this.value = this.value.replace(/[^0-9+/\.]/g,'');

            var curchr = this.value.length;
            var curval = $(this).val();

            if ((curchr > 0 && curchr < 6) && curval.indexOf("+") <= -1) {
                $(this).val("+" + curval);
            } else if (curchr == 6 && curval.indexOf("+") > -1) {
                $(this).val(curval + "/");
            } else if (curval.indexOf("+") <= -1 && curval.indexOf("/") != 6) {
                $(this).val($(this).val().replace(/^(\d{5})(\d{6,13})+$/, "+$1/$2"));
            } else {
                $(this).val(curval);
                $(this).attr('maxlength', '20');
            }
        });
    },

    uploadGallery: function (dropzoneBox) {
        let grid = dropzoneBox.find('.gridly');
        let token = $('meta[name="csrf-token"]').attr('content');
        let keys = {}
        let brickRoute = grid.find('.brick').data('route')
        let galleryType = dropzoneBox.data('gallery')

        if ($('#lang-keys').length) {
            keys = JSON.parse($('#lang-keys').val());
        }
        let options = {
            url: dropzoneBox.find('.dropzone').data('url'),
            autoProcessQueue: true,
            uploadMultiple: true,
            createImageThumbnails: true,
            parallelUploads: 5,
            maxFiles: 500,
            headers: {'X-CSRF-TOKEN': token},
            params: function (file, response) {
                let previewElement = file[0].previewElement
                let root = previewElement.closest('.dropzone-box')
                galleryType = $(root).data('gallery')
                grid = $(root).find('.gridly')
                brickRoute = grid.data('route')

                return {
                    'galleryType': galleryType
                }
            },
            success: function (file, response) {
                if (!response.success) {
                    alert(response.message)
                    return false
                }

                let fileId = 0
                $.each(response.data.file, function (index, dataFile) {
                    if (file.name == dataFile.file_name) {
                        fileId = dataFile.id
                    }
                })

                let html = '<div class="brick small row col-sm-3 text-center" data-route="' + brickRoute + '" data-id="' + fileId +'">' +
                    '<div class="brick-image" style="background-image: url(' + file.dataURL + '); background-size: cover;"></div>' +
                    '<div class="dr-act">' +
                    '<a class="btn btn-success dr-act-status btn-min-w64" ' +
                    'data-status="active">' + Lang.get('common.active') + '</a>' +
                    '<a class="btn btn-danger dr-act-delete btn-min-w64">' + Lang.get('common.delete') + '</a>' +
                    '</div>' +
                    '<input type="hidden" value="' + file.size + '" name="file_size[]">\n' +
                    '<input type="hidden" value="' + file.type + '" name="file_mime_type[]">\n' +
                    '<input type="hidden" value="' + file.name + '" name="file_name[]">\n' +
                    '<input type="hidden" value="1" name="file_active[]">' +
                    '<input type="hidden" value="' + file.dataURL + '" name="file_path[]">\n' +
                    '</div>'
                grid.prepend(html)
                let previewGalleryBox = $('img.preview-' + galleryType)
                previewGalleryBox.attr('src', file.dataURL)

                var _ref;

                if (file.previewElement) {
                    if ((_ref = file.previewElement) != null) {
                        _ref.parentNode.removeChild(file.previewElement);
                    }
                }
            }
        }

        dropzoneBox.find('.dropzone').dropzone(options)
        MainShared.fn.sortableGridly.call(this, grid)

        $(document).on('click', '.dr-act-delete', function () {
            let root = $(this).closest('.brick')
            let gridly = root.closest('.gridly')
            let mediaId = root.data('id')
            let url = gridly.data('route')
            let galleryType = root.closest('.dropzone-box').data('gallery')
            root.remove();
            $.ajax({
                url: url,
                type: 'POST',
                data: {'mediaId': mediaId, 'is_delete': 1, 'galleryType': galleryType},
                success: function (response) {
                    let previewGallery = response.data.previewGallery
                    let previewGalleryBox = $('img.preview-' + galleryType)
                    if (previewGallery) {
                        previewGalleryBox.attr('src', previewGallery)
                    } else {
                        let noImage = $('.no-image').val()
                        previewGalleryBox.attr('src', noImage)
                    }
                },
                error: function (error) {
                    console.log(error)
                }
            })

        });

        $(document).on('click', '.dr-act-status', function () {
            let _this = $(this);
            let root = _this.closest('.brick');
            let status = _this.attr('data-status');
            let inputStatus = root.find('[name="file_active[]"]');
            let mediaId = root.data('id')
            let gridly = root.closest('.gridly')
            let url = gridly.attr('data-route')
            let galleryType = root.closest('.dropzone-box').data('gallery')

            if (status === 'active') {
                inputStatus.val(0);
                _this.attr('data-status', 'inactive');
                _this.removeClass('btn-success').addClass('btn-danger');
                _this.empty().text((typeof keys.inactive != 'undefined' ? keys.inactive : 'Inactive'));
            } else {
                inputStatus.val(1);
                _this.attr('data-status', 'active');
                _this.removeClass('btn-danger').addClass('btn-success');
                _this.empty().text((typeof keys.active != 'undefined' ? keys.active : 'Active'));
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: {'mediaId': mediaId, 'status': inputStatus.val(), 'galleryType': galleryType},
                success: function (response) {
                    let previewGallery = response.data.previewGallery
                    let previewGalleryBox = $('img.preview-' + galleryType)

                    if (previewGallery) {
                        previewGalleryBox.attr('src', previewGallery)
                    } else {
                        let noImage = $('.no-image').val()
                        previewGalleryBox.attr('src', noImage)
                    }
                },
                error: function (error) {
                    console.log(error)
                }
            })
        });
    },

    initDropzone: function () {
        MainShared.fn.uploadGallery.call(this, $('.dropzone-box'))
    },

    /**
     * Change image order function
     *
     * @function sortableGridly
     */
    sortableGridly: function (grid) {
        grid.sortable({
            containerSelector: '.gridly',
            itemSelector: '.brick',
            onDragStart: function ($item, container, _super) {
                let width = $item.width();
                let height = $item.height();
                container.group.placeholder = $('<div class="gridly-placeholder brick small col-sm-3 text-center" ' +
                    'style="width: ' + width + 'px; height: ' + height + 'px;"></div>');

                _super($item, container);
            },
        });
    },

    switchToggleDiv: function() {
        if($('.switch-toggle-div').length > 0) {
            $('.switch-toggle-div').each(function(index, item) {
                $(this).on('change', function() {
                    var toggleName = $(this).attr('data-toggle-div');

                    if($(this).is(':checked')) {
                        $(toggleName).show();
                    }
                    else {
                        $(toggleName).hide();
                    }
                });
            });
        }
    },

    rule: function () {
        $(document).ready(function () {
            MainShared.fn.init.call(this);
        });
    },
};

MainShared.fn.rule();
