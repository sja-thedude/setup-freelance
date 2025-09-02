{{--
Add HTML classs "btn-show-forgot-password-modal" to show Forgot Password modal
--}}

<div id="modalForgotPassword" class="user-modal hidden modal-authen">
    <div class="pop-up">
        <div class="row">
            <div class="col-md-6 col-md-push-3">
                <form action="{{ route('api.password.email') }}" method="POST" name="form_forgot_password" novalidate>
                    <div class="wrap-popup-card">
                        <a href="javascript:;" class="close"
                            data-dismiss="popup" data-target="#modalForgotPassword"><i class="icn-close"></i></a>
                        <div class="wp-card">
                            <div class="row modal-error-title">
                                <h5>@lang('auth.forgot_password')</h5>
                            </div>
                            <div class="form-line">
                                <div class="form-input">
                                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16.9333 17.4001C16.6593 17.5877 16.3318 17.6813 16 17.6668C15.6682 17.6813 15.3407 17.5877 15.0667 17.4001L8 13.6001V20.3334C8 21.438 8.89543 22.3334 10 22.3334H22C23.1046 22.3334 24 21.438 24 20.3334V13.6001L16.9333 17.4001Z" fill="#8898AA"></path>
                                        <path d="M22 9H10C8.89544 9 8.00001 9.89543 8.00001 11V11.6667C7.99871 11.9112 8.12507 12.1386 8.33334 12.2667L15.6667 16.2667C15.769 16.3206 15.8848 16.3437 16 16.3333C16.1152 16.3437 16.231 16.3206 16.3333 16.2667L23.6667 12.2667C23.8749 12.1386 24.0013 11.9112 24 11.6667V11C24 9.89543 23.1046 9 22 9Z" fill="#8898AA"></path>
                                    </svg>
                                    <input type="email" name="email" class="location email required" placeholder="@lang('auth.email_short')">
                                </div>
                            </div>
                            <button type="submit" disabled class="btn btn-modal btn-recover-password">@lang('auth.restore_password')</button>
                            <div class="error" style="margin-top: 20px;"></div>
                            <a href="javascript:;" class="modal-back-text btn-show-login-modal back-link-normal">@lang('auth.forgot_password_modal.button_back_naar')</a>
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
                            <img width="106" height="91" src="{!! url("/images/home/icon-success-order.svg") !!}">
                            <span class="modal-error-content">@lang('auth.forgot_password_modal.confirmation_description')</span>
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