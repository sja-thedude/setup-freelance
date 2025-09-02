function Manager() {
}

Manager.fn = {
    init: function () {
        Manager.fn.showConfirmDeletePopupSubmit.call(this);
        Manager.fn.loadAssignDelete.call(this);
        Manager.fn.submitAssignDelete.call(this);
        Manager.fn.callAjax.call(this);
        Manager.fn.submitCreateForm.call(this);
        Manager.fn.handleCancelDelete.call(this);
        Manager.fn.pickColor.call(this);
    },

    handleCancelDelete: function() {
        $(document).on('click', '.account-manager-reset', function(){
            var modal = $(this).closest('.modal');

            //Reset select2
            if (modal.find('.select2').length || modal.find('.select2-tag').length) {
                modal.find('.select2, .select2-tag').val('').trigger('change');
            }

            modal.find('[aria-invalid="true"]').removeClass('error').attr('aria-invalid', false);
            modal.find('.form-control, .select2').removeClass('error').attr('aria-invalid', false);
        });
    },

    submitCreateForm: function() {
        $('#create_manager').validate({
            onkeyup: false,
            onfocusout: false,
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
            submitHandler: function(form) {
                if ($(form).valid()) {
                    var selfForm = $(form);
                    var url = selfForm.attr('action');
                    var method = selfForm.attr('method');
                    var submit = selfForm.find('[type="submit"]');
                    var data = selfForm.serializeArray();
                    $('body').loading('toggle');
                    submit.attr('disabled', 'disabled');
                    
                    $.ajax({
                        url: url,
                        type: method,
                        data: data
                    }).success(function (response) {
                        if(response.success == true) {
                            location.reload();
                        } else {
                            if(response.data) {
                                MainShared.fn.showErrorMessages(response, selfForm);
                            }
                        }
                    }).error(function(XMLHttpRequest, textStatus, errorThrown) {
                        var response = XMLHttpRequest.responseJSON;

                        if(response.data) {
                            MainShared.fn.showErrorMessages(response, selfForm);
                        }
                    }).always(function() {
                        submit.removeAttr('disabled');
                        $('body').loading('toggle');
                    });
                }
            }
        });
    },
    
    callAjax: function() {
        $(document).on('click', '.call-ajax', function () {
            var _this = $(this);
            var route = _this.data('route');
            var method = _this.data('method');
            var closeLabel = _this.data('close_label');
            var deletedLabel = _this.data('deleted_success');
            var modal = _this.closest('.modal');
            var _token = $('meta[name="csrf-token"]').val();
            var data = {token: _token};

            $('body').loading('toggle');
            $.ajax({
                url: route,
                method: method,
                data: data,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        modal.modal('hide');
                        $("#tr-" + _this.data('id')).find('.status').empty().text(Lang.get('manager.status_2'));
                        Swal.fire({
                            title: '<span class="ir-popup-title">' + Lang.get('manager.sent_invitation_success') + '</span>',
                            html: '<span class="ir-popup-content">' + response.message + '</span>',
                            width: 512,
                            padding: '43px 60px 30px 60px',
                            showConfirmButton: false,
                            showCloseButton: true,
                            showCancelButton: true,
                            cancelButtonText: Lang.get('workspace.close')
                        });
                    } else {
                        toastr.error(response.message);
                    }

                    $('body').loading('toggle');
                }
            });
        });
    },
    
    submitAssignDelete: function() {
        $(document).on('click', '.assign-delete', function () {
            var _this = $(this);
            var disabled = _this.attr('disabled');
            
            if(typeof disabled == 'undefined') {
                var route = _this.data('route');
                var _token = $('meta[name="csrf-token"]').val();
                var managerId = _this.closest('.modal').find('.another-manager').val();
                var modal = _this.closest('.modal');
                var data = {token: _token, manager_id: managerId};
                
                $('body').loading('toggle');
                $.ajax({
                    url: route,
                    method: 'DELETE',
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            modal.modal('hide');
                            $("#tr-" + _this.data('id')).hide();
                            Swal.fire({
                                title: '<span class="ir-popup-title">' + Lang.get('manager.deleted_successfully') + '</span>',
                                html: '<span class="ir-popup-content">' + response.message + '</span>',
                                width: 512,
                                padding: '43px 60px 30px 60px',
                                showConfirmButton: false,
                                showCloseButton: true,
                                showCancelButton: true,
                                cancelButtonText: Lang.get('workspace.close')
                            });
                        } else {
                            toastr.error(response.message);
                        }

                        $('body').loading('toggle');
                    }
                });
            }
        });
    },
    
    checkButtonAssignDelete: function(_this) {
        var select = _this.val();
        var assignDelete = _this.closest('.modal').find('.assign-delete');

        if(select != '') {
            assignDelete.removeAttr('disabled');
        } else {
            assignDelete.attr('disabled', 'disabled');
        }
    },
    
    loadAssignDelete: function() {
        $('.another-manager').map(function(){
            var _this = $(this);
            Manager.fn.checkButtonAssignDelete(_this);
        });  
    },
    
    showConfirmDeletePopupSubmit: function () {
        $(document).on('click', '.show-delete-confirm', function () {
            //Close dropdown
            $(".ir-actions").parent().removeClass('open');

            var _this = $(this);
            var target = _this.data('target');
            var select = $(target).find('.another-manager');
            Manager.fn.checkButtonAssignDelete(select);
        });

        $(document).on('change', '.another-manager', function () {
            var _this = $(this);
            Manager.fn.checkButtonAssignDelete(_this);
        });
    },

    pickColor: function () {
        $('.color-picker-button').ColorPicker({
            onChange: function (hsb, hex, rgb) {
                $('.color-picker-input').val('#' + hex).trigger('change');
                $('.color-picker-button span').css('backgroundColor', '#' + hex);
            }
        });

        $('.color-picker-input').ColorPicker({
            onChange: function (hsb, hex, rgb) {
                $('.color-picker-input').val('#' + hex).trigger('change');
                $('.color-picker-button span').css('backgroundColor', '#' + hex);
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

    rule: function () {
        $(document).ready(function () {
            Manager.fn.init.call(this);
        });
    },
};

Manager.fn.rule();