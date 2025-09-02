{{--
Add HTML classs "btn-show-forgot-password-modal" to show Forgot Password modal
--}}

<div id="modalForgotPassword" class="user-modal hidden">
    <div class="pop-up">
        <div class="row">
            <div class="col-md-6 col-md-push-3">
                <form action="{{ route('api.password.email') }}" method="POST" name="form_forgot_password" novalidate>
                    <div class="wrap-popup-card">
                        <a href="javascript:;" class="modal-back-text btn-show-login-modal">@lang('auth.forgot_password_modal.button_back')</a>
                        <a href="javascript:;" class="close"
                            data-dismiss="popup" data-target="#modalForgotPassword"><i class="icn-close"></i></a>
                        <div class="wp-card">
                            <div class="row modal-error-title">
                                <h5>@lang('auth.forgot_password_modal.title')</h5>
                            </div>
                            <div class="row">
                                <span class="modal-error-content">@lang('auth.forgot_password_modal.description')</span>
                            </div>
                            <div class="form-line">
                                <div class="form-input">
                                    <input type="email" name="email" class="location pl-25" placeholder="@lang('auth.email')">
                                </div>
                                <div class="error"></div>
                            </div>
                            <div class="modal-register-form">
                                <div class="row custom-col">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-modal btn-recover-password btn-pr-custom">@lang('auth.restore_password')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modalForgotPasswordConfirmation" class="user-modal hidden">
    <div class="pop-up">
        <div class="row">
            <div class="col-md-6 col-md-push-3">
                <div class="wrap-popup-card">
                    <a href="javascript:;" class="close" data-dismiss="popup"><i class="icn-close"></i></a>
                    <div class="wp-card">
                        <div class="row modal-error-title">
                            <h5>@lang('auth.forgot_password_modal.confirmation_title')</h5>
                        </div>
                        <div class="row">
                            <span class="modal-error-content">@lang('auth.forgot_password_modal.confirmation_description')</span>
                        </div>
                        <button type="button" class="btn btn-modal" data-dismiss="modal" style="line-height: normal;">@lang('common.close')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(function () {
            var modalForgotPassword = $('#modalForgotPassword');
            var formForgotPassword = modalForgotPassword.find('form[name="form_forgot_password"]');
            var errorContainer = modalForgotPassword.find('.error');

            // Btn show Forgot Password modal
            var btnShowForgotPasswordModal = $('.btn-show-forgot-password-modal');

            // Show Forgot Password modal
            btnShowForgotPasswordModal.on('click', function (e) {
                e.preventDefault();

                $('#modalLogin').addClass('hidden');
                $('#modalForgotPassword').removeClass('hidden');

                // return false;
            });

            formForgotPassword.on('submit', function (e) {
                e.preventDefault();

                // Hide error
                formForgotPassword.removeClass('invalid');
                errorContainer.text('');

                // Get roles by workspace
                $.ajax({
                    type: formForgotPassword.attr('method'),
                    url: formForgotPassword.attr('action'),
                    headers: {
                        'X-CSRF-TOKEN': '{!! csrf_token() !!}',
                        'Content-Language': '{{App::getLocale()}}'
                    },
                    data: formForgotPassword.serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Hide current modal
                            $('#modalForgotPassword').addClass('hidden');
                            // Show confirmation modal
                            $('#modalForgotPasswordConfirmation').removeClass('hidden');
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (error) {
                        var response = error.responseJSON;

                        // Show error
                        formForgotPassword.addClass('invalid');
                        errorContainer.text(response.message);
                    },
                });

                return false;
            });
        });
    </script>
@endpush