function Profile() {
}

Profile.fn = {
    init: function () {
        Profile.fn.submitCreateFormProfile.call(this);
        Profile.fn.submitChangePasswordFormProfile.call(this);
    },
    
    submitCreateFormProfile: function() {
        $('.manager-profile').map(function(){
            $(this).validate({
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
                                var body = selfForm.find('.modal-body');
                                var view = body.find('fieldset[data-id="view-profile-area"]');
                                var edit = body.find('fieldset[data-id="edit-profile-area"]');
                                var profileName = $('.profile-name');

                                view.remove();
                                edit.remove();
                                body.append(response.data.viewRender);
                                body.append(response.data.editRender);
                                MainShared.fn.setAfterChanged.call(this);
                                profileName.empty().text(response.data.user.name);
                                selfForm.find('.show-hide-actions[data-target="view-profile-area"]').trigger('click');
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
                            $('body').loading('toggle');
                        });
                    }
                }
            });
        });
    },

    submitChangePasswordFormProfile: function() {
        $('.manager-change-password').map(function(){
            $(this).validate({
                onkeyup: false,
                onfocusout: false,
                rules: {
                    current_password: {
                        customRequired: true,
                    },
                    new_password: {
                        customRequired: true,
                    },
                    password_confirmation: {
                        customRequired: true,
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
                                form.reset();
                                $('#modal_change_password').modal('hide');
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
        });
    },
    
    rule: function () {
        $(document).ready(function () {
            Profile.fn.init.call(this);
        });
    },
};

Profile.fn.rule();