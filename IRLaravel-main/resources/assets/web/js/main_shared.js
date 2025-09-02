function MainShared() {
}

MainShared.fn = {
    init: function () {
        MainShared.fn.updateStatus.call(this);
        MainShared.fn.onKeypressSearch.call(this);
        MainShared.fn.showEditForm.call(this);
        MainShared.fn.closeEditForm.call(this);
        MainShared.fn.submitCreateForm.call(this);
        MainShared.fn.submitUpdateForm.call(this);
        MainShared.fn.readUrlImage.call(this);
        // MainShared.fn.initSelect2Tags.call(this);
        // MainShared.fn.select2Type.call(this);
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
        MainShared.fn.checkSubmitButton.call(this);
        MainShared.fn.validateMin.call(this);
        MainShared.fn.validateMax.call(this);
        MainShared.fn.resetWhenCloseModal.call(this);
        MainShared.fn.hideWhenClick.call(this);
        MainShared.fn.autoSubmit.call(this);
        MainShared.fn.convertUtcToLocalTime.call(this);
        MainShared.fn.autoDetectTimeZone.call(this);
        MainShared.fn.expand.call(this);
        // MainShared.fn.datePicker.call(this);
        MainShared.fn.checkChangeForm.call(this);
        // MainShared.fn.triggerDateRange.call(this);
        MainShared.fn.autoFillSlug.call(this);
    },

    autoFillSlug: function() {
        $(document).on('change', '.fill-slug', function(){
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

    triggerDateRange: function() {
        $(document).on('click', '.ir-btn-date-range', function(){
            var input = $(this).closest('.ir-group-date-range').find('.ir-input');

            if(input.length) {
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
                if(!form.hasClass('only-reset-validate')) {
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
            return this.optional(element) || /^\+{1}[0-9]{8,19}$/.test(value);
        }, Lang.get('common.validation.phone_valid'));
    },

    emailValid: function () {
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
            if ($('.ir-modal').length > 0) {
                // $('.ir-modal').modal('hide');
            } else if ($(this).closest('.user-modal').length > 0) {
                $(this).closest('.user-modal').hide();
            }

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
        selfForm.find('.form-control, .select2').each(function(k, v) {
            $(v).removeClass('error');
        });

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

            if (input.length) {
                input.removeClass('error').addClass('error').attr('aria-invalid', true);

                if (inputError.length) {
                    inputError.empty().text(message).show();
                } else {
                    input.after('<label id="' + index + '-error" class="error" for="' + index + '">' + message + '</label>');
                }
            }
        });
    },

    showToastError: function(response) {
        var messages = '';

        $.each(response.data, function (index, message) {
            messages += message + ' ';
        });

        toastr.error(messages);
    },

    processFormByAjax: function (form, type) {
        if ($(form).valid()) {
            // $('body').loading('toggle');
            var elem = $(form);
            var formData = new FormData(elem[0]);
            var currentUrl      = window.location.href;
            

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
                    if (response.success == true) {
                        // Popup show address box
                        if ($('input[name=show_address_box]').length && MainWeb.fn.getUrlParameter('tab')) {
                            window.location.href = currentUrl + "&show_address_box=true";
                        // Popup profile when first login social
                        } else if (MainWeb.fn.getUrlParameter('profile')) {
                            window.location.href = window.DOMAIN;
                        } else {
                            location.reload();
                        }
                    } else {
                        if (response.data) {
                            MainShared.fn.showErrorMessages(response, elem);
                        }
                    }
                }
            }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                var response = XMLHttpRequest.responseJSON;

                if (response.data) {
                    MainShared.fn.showErrorMessages(response, elem);
                    $("#update_profile .mgb-20").removeClass('mgb-20');
                }
            }).always(function () {
                elem.removeClass('disabled');
                elem.find('.save-form').removeAttr('disabled');
                // $('body').loading('toggle');
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

        let drpOptions = {
            // drops: 'auto',
            minDate: new Date(),
            singleDatePicker: true,
            timePicker: true,
            drops: position,
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

        $('.ir-datepicker').daterangepicker(drpOptions, function(start, end, label) {
            MainShared.fn.setDefautDateInput(start);
        });

        if (dateRange.length) {
            var startDateOrigin = dateRange.find('.start_date').val();
            var objDaterangepicker = objDatepicker.data('daterangepicker');

            if (startDateOrigin !== '') {
                objDaterangepicker.setStartDate(moment(startDateOrigin));

                if (dateRange.find('.start_date').data('single-date')) {
                    objDaterangepicker.setEndDate(moment(startDateOrigin));
                }
            } else {
                MainShared.fn.setDefautDateInput(startDate);
            }
        }

        objDatepicker.on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY HH:mm'));
            MainShared.fn.setDefautDateInput(picker.startDate);
        });

        objDatepicker.on('cancel.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY HH:mm'));
            MainShared.fn.setDefautDateInput(picker.startDate);
        });
        
        $(document).on('click', '.date-range-trigger', function () {
            objDatepicker.trigger('click');
        });
    },

    setDefautDateInput: function (startDate) {
        var dateRange = $('.ir-group-datepicker');

        if (dateRange.length) {
            dateRange.find('.start_date').val(startDate.format('YYYY-MM-DD HH:mm:ss'));
        }
    },

    rule: function () {
        $(document).ready(function () {
            MainShared.fn.init.call(this);
        });
    },
};

MainShared.fn.rule();