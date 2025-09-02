{{--
Add HTML classs "btn-show-login-modal" to show Register modal
--}}

<div id="modalRegister" class="user-modal hidden">
    <div class="bg"></div>
    <div class="pop-up">
        <div class="row">
            <div class="col-md-7 col-md-push-3">
                <form action="{{ route('api.submit_register') }}" method="POST" name="form_register" novalidate id="form-register">
                    <input type="hidden" name="platform" value="web">
                    <div class="wrap-popup-card">
                        <a href="javascript: ;" class="modal-back-text btn-show-login-modal">@lang('auth.register_modal.button_back')</a>
                        <a href="javascript:;" class="close"
                            data-dismiss="popup" data-target="#modalRegister"><i class="icn-close"></i></a>
                        <div class="wp-card">
                            <div class="row modal-error-title">
                                <h5>@lang('auth.register_modal.title')</h5>
                            </div>
                            <div class="">
                                <span class="modal-error-content">@lang('auth.register_modal.description')</span>
                            </div>
                            <div class="modal-register-form">
                                <div class="row custom-col">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-line-modal" data-line="line-1">
                                                    <div class="input-container">
                                                        <input type="text" name="first_name" class="Gianluca" placeholder="@lang('register.placeholders.first_name')">
                                                    </div>
                                                    <div class="error" ></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-line-modal" data-line="line-2">
                                                    <div class="input-container">
                                                        <input type="text" name="last_name" class="Punzo" placeholder="@lang('register.placeholders.last_name')">
                                                    </div>
                                                    <div class="error" ></div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                        <div class="form-line-modal" data-line="line-3">
                                            <div class="input-container">
                                                <input type="text" name="gsm" class="Punzo keyup-gsm" placeholder="@lang('register.placeholders.gsm')">
                                            </div>
                                            <label class="font-weight">@lang('register.descriptions.gsm')</label>
                                            <div class="error" ></div>
                                        </div>
                                        <div class="form-line-modal" data-line="line-4">
                                            <div class="input-container">
                                                <input type="email" name="email" class="Punzo" placeholder="@lang('register.placeholders.email')">
                                            </div>
                                            <div class="error last-row-error" ></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-line-modal" data-line="line-3">
                                            <div class="input-container custom-form-input">
                                                <input type="password" name="password" class="dob" placeholder="@lang('register.placeholders.password')">
                                                <span class="right show-pass active">
                                                    <svg class="svg-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    <svg class="svg-icon hidden" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M5.378 1.31812L19.318 23.6221M1 12.5001C1 12.5001 5 4.83345 12 4.83345C19 4.83345 23 12.5001 23 12.5001C23 12.5001 19 20.1668 12 20.1668C5 20.1668 1 12.5001 1 12.5001ZM15 12.5001C15 14.0879 13.6569 15.3751 12 15.3751C10.3431 15.3751 9 14.0879 9 12.5001C9 10.9123 10.3431 9.62512 12 9.62512C13.6569 9.62512 15 10.9123 15 12.5001Z" stroke="#BDBDBD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="error" ></div>
                                        </div>
                                        <div class="form-line-modal" data-line="line-4">
                                            <div class="input-container custom-form-input">
                                                <input type="password" name="password_confirmation" class="dob" placeholder="@lang('register.placeholders.password_confirmation')">
                                                <span class="right show-pass active">
                                                    <svg class="svg-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    <svg class="svg-icon hidden" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M5.378 1.31812L19.318 23.6221M1 12.5001C1 12.5001 5 4.83345 12 4.83345C19 4.83345 23 12.5001 23 12.5001C23 12.5001 19 20.1668 12 20.1668C5 20.1668 1 12.5001 1 12.5001ZM15 12.5001C15 14.0879 13.6569 15.3751 12 15.3751C10.3431 15.3751 9 14.0879 9 12.5001C9 10.9123 10.3431 9.62512 12 9.62512C13.6569 9.62512 15 10.9123 15 12.5001Z" stroke="#BDBDBD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </span>
                                            </div>
{{--                                            <label class="hidden-mobile" style="visibility: hidden">&nbsp;</label>--}}
                                            <div class="error" style="height: 25px;"></div>
                                        </div>
                                        <div class="form-line-modal">
                                            <div class="wrap-checkbox-register">
                                                <input type="checkbox" name="checkbox-register" id="checkbox-1" class="checkbox-register pull-left" style="border: none;"/>
                                                <label for="checkbox-1">@lang('register.text_terms_and_conditions')</label>
                                            </div>
                                            <div class="error error-checkbox" style="margin-top: 0 !important;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="error" style="margin-bottom: 20px;"></div>
                            </div>
                            <button type="submit" class="btn btn-modal btn-modal-register">@lang('auth.register_modal.button_register')</button>
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    @include('auth.social_login', [
                                        'site' => 'register_with',
                                        'horizontal' => true
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modalRegisterConfirmation" class="user-modal hidden">
    <div class="bg"></div>
    <div class="pop-up">
        <div class="row">
            <div class="col-md-6 col-md-push-3">
                <div class="wrap-popup-card">
                    <a href="javascript:;" class="close" data-dismiss="popup"><i class="icn-close"></i></a>
                    <div class="wp-card">
                        <div class="row modal-error-title">
                            <h5>@lang('auth.register_modal.confirmation_title')</h5>
                        </div>
                        <div class="row">
                            <span class="modal-error-content">@lang('auth.register_modal.confirmation_description')</span>
                        </div>
                        <div class="modal-register-form">
                            <div class="row custom-col">
                            </div>
                        </div>
                        <button type="button" class="btn btn-modal btn-pr-custom" data-dismiss="modal" style="line-height: normal;">@lang('common.close')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(function () {
            var modalRegister = $('#modalRegister');
            var formRegister = modalRegister.find('form[name="form_register"]');
            var txtBirthdayDisplay = formRegister.find(':input[name="birthday_display"]');
            var txtBirthday = formRegister.find(':input[name="birthday"]');
            var chkAgree = formRegister.find('[name="checkbox-register"]');
            // var errorContainer = modalRegister.find('.error');
            var requiredFields = ['first_name', 'last_name', 'gsm', 'email', 'password', 'password_confirmation'];

            // Apply change from birthday display field to birthday field
            txtBirthdayDisplay.on('change', function () {
                var value = $(this).val();
                var date = moment(value, "DD/MM/YYYY");
                var valFormat = date.format('YYYY-MM-DD');

                if (!date || !valFormat) {
                    // Process when invalid value
                    valFormat = '';
                }

                // Set value for submit data
                txtBirthday.val(valFormat);
            });

            // Btn show register modal
            var btnShowRegisterModal = $('.btn-show-register-modal');

            // Show register modal
            btnShowRegisterModal.on('click', function (e) {
                e.preventDefault();

                $('#modalLogin').addClass('hidden');
                $('#modalRegister').removeClass('hidden');

                // return false;
            });

            /**
             * Validate fields
             *
             * @returns {boolean}
             */
            var isValidForm = function () {
                for (var k in requiredFields) {
                    var fieldName = requiredFields[k];
                    var field = formRegister.find('[name="' + fieldName + '"]');

                    // Check is required
                    if (field.length > 0 && field.val() === '') {
                        return false;
                    }
                }

                return true;
            };

            // Highlight Terms & Conditions when not check
            chkAgree.change(function () {
                if (this.checked) {
                    //Do stuff
                    modalRegister.find('.wrap-checkbox-register').removeClass('invalid-input');
                }
            });

            // Submit form
            formRegister.on('submit', function (e) {
                e.preventDefault();

                // Hide error
                formRegister.removeClass('invalid');
                // errorContainer.text('');
                formRegister.find('.error')
                    .text('')
                    .removeClass('show-error');
                formRegister.find('.invalid-input')
                    .removeClass('invalid-input');

                // Get roles by workspace
                $.ajax({
                    type: formRegister.attr('method'),
                    url: formRegister.attr('action'),
                    headers: {
                        'X-CSRF-TOKEN': '{!! csrf_token() !!}'
                    },
                    data: formRegister.serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Hide current modal
                            $('#modalRegister').addClass('hidden');
                            // Show confirmation modal
                            $('#modalRegisterConfirmation').removeClass('hidden');
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (error) {
                        var response = error.responseJSON;

                        if (response.data) {
                            var data = response.data;

                            for (var fieldName in data) {
                                var field = formRegister.find('[name="' + fieldName + '"]').first();

                                if (field.length > 0) {
                                    var fieldContainer = field.closest('.form-line-modal');

                                    if (fieldContainer.length > 0) {
                                        var fieldError = data[fieldName];
                                        var fieldErrorMessage = (fieldError && fieldError.length > 0) ? fieldError[0] : '';
                                        var line = fieldContainer.data('line')
                                        $(fieldContainer).find('.error')
                                            .text(fieldErrorMessage);
                                        $('[data-line=' + line + ']').find('.error').addClass('show-error');

                                        var fieldBox = fieldContainer.find('.input-container');

                                        if (fieldName == 'checkbox-register') {
                                            fieldBox = fieldContainer.find('.wrap-checkbox-register')
                                        }

                                        if (fieldBox.length > 0) {
                                            fieldBox.addClass('invalid-input');
                                        }
                                    }
                                }
                            }
                        }

                        // Show error
                        formRegister.addClass('invalid');
                    },
                });

                return false;
            });
        });
        function keyPressMobile () {
            let currentWidth = $(window).width()
            if (currentWidth <= 768) {
                return true;
            }else
                return false;
        }
    </script>
@endpush