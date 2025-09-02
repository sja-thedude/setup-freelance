{{--
Add HTML classs "btn-show-login-modal" to show Register modal
--}}

<div id="modalRegister" class="user-modal hidden">
    <div class="bg"></div>
    <div class="pop-up">
        <div class="row">
            <div class="col-md-7 col-md-push-3">
                <form action="{{ route('api.submit_register') }}" method="POST" name="form_register" novalidate>

                    <input type="hidden" name="platform" value="web">

                    <div class="wrap-popup-card">
                        <a href="javascript: ;" class="modal-back-text">@lang('auth.register_modal.button_back')</a>
                        <a href="javascript:;" class="close"
                            data-dismiss="popup" data-target="#modalRegister"><i class="icn-close"></i></a>
                        <div class="wp-card">
                            <div class="row modal-error-title">
                                <h5>@lang('auth.button_register')</h5>
                            </div>
                            <div class="modal-register-form">
                                <div class="row custom-col owl-field-group">
                                    <div class="col-md-12 group-1">
                                        <div class="form-line-modal" data-line="line-1">
                                            <div class="input-container">
                                                <input type="text" name="first_name" class="Gianluca" placeholder="@lang('register.placeholders.first_name')">
                                            </div>
                                            <div class="error"></div>
                                        </div>
                                        <div class="form-line-modal" data-line="line-2">
                                            <div class="input-container">
                                                <input type="text" name="last_name" class="Punzo" placeholder="@lang('register.placeholders.last_name')">
                                            </div>
                                            <div class="error"></div>
                                        </div>
                                        <div class="form-line-modal" data-line="line-3">
                                            <div class="input-container">
                                                {{-- data-date-format attribute: moment js date format --}}
                                                @php($momentDateFormat = strtoupper(Helper::getJsDateFormat()))
                                                {{-- Get last of birthday by the rule: Minimum 13 years and 1 day is mandatory to be able to register. --}}
                                                @php($defaultBirthday = Helper::getBirthdayBeforeDate())
                                                <input type="text" name="birthday_display" class="location show-date"
                                                       placeholder="@lang('register.placeholders.birthday')" autocomplete="off"
                                                       data-date-format="{{ $momentDateFormat }}"
                                                       data-max-date="{{ $defaultBirthday }}"
                                                       onkeypress="keyPressMobile()">
                                                <input type="hidden" name="birthday">
                                            </div>
                                            <div class="error"></div>
                                        </div>
                                        <div class="form-line-modal use-maps" style="margin-bottom: 6px;" data-line="line-4">
                                            <div class="input-container">
                                                <div class="form-input maps">
                                                    <i class="icn-location left"></i>
                                                    <input type="text" name="address" class="location pl-35"
                                                           placeholder="@lang('register.placeholders.address')" autocomplete="off">
                                                    <input type="hidden" name="lat" class="latitude">
                                                    <input type="hidden" name="lng" class="longitude">
                                                </div>

                                                <ul class="place-results" id="address-box" style="left: 0 !important;"></ul>
                                            </div>
                                            <label class="hint">@lang('register.descriptions.address')</label>
                                            <div class="error"></div>
                                        </div>
                                        <div class="form-line-modal choose-gender" data-line="line-5">
                                            <div class="input-container">
                                                <div class="checkbox-sex checkbox-color">
                                                    <div class="wrap-content">
                                                        <input type="radio" id="gender_male" checked="" name="gender" class="radio-sex" value="{{ \App\Models\User::GENDER_MALE }}">
                                                        <label for="gender_male" class="slider active">@lang('register.options.gender.male')</label>
                                                    </div>
                                                    <div class="wrap-content">
                                                        <input type="radio" id="gender_female" name="gender" class="radio-sex" value="{{ \App\Models\User::GENDER_FEMALE }}">
                                                        <label for="gender_female" class="slider">@lang('register.options.gender.female')</label>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </div>
                                            <div class="error"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 group-2">
                                        <div class="form-line-modal" style="margin-bottom: 6px;" data-line="line-5">
                                            <div class="input-container">
                                                <input type="text" name="gsm" class="Punzo keyup-gsm" placeholder="@lang('register.placeholders.gsm')">
                                            </div>
                                            <label class="hint">@lang('register.descriptions.gsm')</label>
                                            <div class="error"></div>
                                        </div>
                                        <div class="form-line-modal" data-line="line-6">
                                            <div class="input-container">
                                                <input type="email" name="email" class="Punzo" placeholder="@lang('register.placeholders.email')">
                                            </div>
                                            <div class="error"></div>
                                        </div>
                                        <div class="form-line-modal" data-line="line-7">
                                            <div class="input-container">
                                                <input type="password" name="password" class="dob password" placeholder="@lang('register.placeholders.password')">
                                                <svg class="icon-eye right show-pass" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 12.5C1 12.5 5 4.16663 12 4.16663C19 4.16663 23 12.5 23 12.5C23 12.5 19 20.8333 12 20.8333C5 20.8333 1 12.5 1 12.5Z" stroke="#8F9FAF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M12 15.625C13.6569 15.625 15 14.2259 15 12.5C15 10.7741 13.6569 9.375 12 9.375C10.3431 9.375 9 10.7741 9 12.5C9 14.2259 10.3431 15.625 12 15.625Z" stroke="#8F9FAF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <div class="error"></div>
                                        </div>
                                        <div class="form-line-modal" data-line="line-8">
                                            <div class="input-container">
                                                <input type="password" name="password_confirmation" class="dob password" placeholder="@lang('register.placeholders.password_confirmation')">
                                                <svg class="icon-eye right show-pass" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 12.5C1 12.5 5 4.16663 12 4.16663C19 4.16663 23 12.5 23 12.5C23 12.5 19 20.8333 12 20.8333C5 20.8333 1 12.5 1 12.5Z" stroke="#8F9FAF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M12 15.625C13.6569 15.625 15 14.2259 15 12.5C15 10.7741 13.6569 9.375 12 9.375C10.3431 9.375 9 10.7741 9 12.5C9 14.2259 10.3431 15.625 12 15.625Z" stroke="#8F9FAF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <div class="error"></div>
                                        </div>
                                        <div class="form-line-modal wrap-checkbox" data-line="line-9">
                                            <div class="wrap-checkbox-register">
                                                <input type="checkbox" name="checkbox-register" id="checkbox-1" class="checkbox-register" style="border: none;"/>
                                                <label for="checkbox-1">@lang('register.text_terms_and_conditions')</label>
                                            </div>
                                            <div class="error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-modal btn-modal-register">@lang('register.next_one')</button>
                            <div class="owl-dots">
                                <div class="owl-dot group-1 active"><span></span></div>
                                <div class="owl-dot group-2"><span></span></div>
                            </div>
                            <div class="row social-register-portal">
                                <div class="common-errors"></div>
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
                            <img width="106" height="91" src="{!! url('/images/home/icon-success-order.svg') !!}">
                            <span class="modal-error-content">@lang('auth.register_modal.confirmation_description')</span>
                        </div>
                        <button type="button" class="btn btn-modal" data-dismiss="modal" style="line-height: normal;">@lang('auth.forgot_password_modal.button_back_naar_itsready')</button>
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
            var errorContainer = modalRegister.find('.error');
            var requiredFields = ['first_name', 'last_name', 'gsm', 'email', 'address', 'birthday', 'password', 'password_confirmation'];
            var commonErrors = formRegister.find('.common-errors');

            // Apply change from birthday display field to birthday field
            txtBirthdayDisplay.on('change', function () {
                var value = $(this).val();

                // Empty value
                if ((value + '').trim() === '') {
                    txtBirthday.val('');
                    return;
                }

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
                
                // Check Term & conditions
                if (!chkAgree.prop('checked')) {
                    modalRegister.find('.wrap-checkbox-register').addClass('invalid-input');

                    if(commonErrors.length) {
                        commonErrors.empty();
                        commonErrors.append('<div class="error show-error">@lang('register.checked_condition_term_policy')</div>');
                    }
                    
                    return false;
                }
        
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
                        if(commonErrors.length) {
                            commonErrors.empty();
                        }

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
                        if(commonErrors.length) {
                            commonErrors.empty();
                        }

                        var response = error.responseJSON;
                        var groupFirst = ['first_name', 'last_name', 'address'];
                        var groupSecond = ['gsm', 'email', 'password', 'password_confirmation'];
                        var isGroupFirst = 0;

                        if (response.data) {
                            var data = response.data;

                            for (var fieldName in data) {
                                var field = formRegister.find('[name="' + fieldName + '"]').first();

                                if(groupFirst.indexOf(fieldName) != -1) {
                                    isGroupFirst++
                                }
                                if (field.length > 0) {
                                    var fieldContainer = field.closest('.form-line-modal');

                                    if (fieldContainer.length > 0) {
                                        var fieldError = data[fieldName];
                                        var fieldErrorMessage = (fieldError && fieldError.length > 0) ? fieldError[0] : '';
                                        var line = fieldContainer.data('line')

                                        fieldContainer.find('.error')
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
                            if(isGroupFirst == 0){
                                $('.group-2').trigger('click');
                            }else {
                                $('.group-1').trigger('click');
                            }
                        }

                        // Show error
                        formRegister.addClass('invalid');
                        var errorMessages = [];

                        if(commonErrors.length) {
                            formRegister.find('.show-error').map(function() {
                                if($(this).text() != '' && !errorMessages.includes($(this).text())) {
                                    errorMessages.push($(this).text());
                                    commonErrors.append($(this).clone());
                                    return false;
                                }
                            });
                        }
                    },
                });

                return false;
            });

            $('.owl-dots .owl-dot').on('click', function (){
                if ($(this).hasClass('active')) {
                    return false
                }
                $(this).addClass('active')

                if ($(this).hasClass('group-1')) {
                    $('.owl-field-group .group-2').hide()
                    $('.owl-field-group .group-1').show()
                    $('.owl-dots .group-2').removeClass('active')
                    $('#modalRegister .modal-back-text').addClass('btn-show-login-modal')
                    $('#modalRegister .modal-back-text').removeClass('back-group-1')
                } else {
                    $('#modalRegister .modal-back-text').removeClass('btn-show-login-modal')
                    $('#modalRegister .modal-back-text').addClass('back-group-1')
                    $('.owl-field-group .group-1').hide()
                    $('.owl-field-group .group-2').show()
                    $('.owl-dots .group-1').removeClass('active')
                }
            })

            $('#modalRegister').on('click', '.modal-back-text', function(e){
                if ($(this).hasClass('back-group-1')) {
                    $('.owl-dots .owl-dot.group-1').trigger('click')
                } else {
                    $('#modalRegister').addClass('hidden')
                    $('#modalLogin').removeClass('hidden');
                }
            })
        });
    </script>
@endpush