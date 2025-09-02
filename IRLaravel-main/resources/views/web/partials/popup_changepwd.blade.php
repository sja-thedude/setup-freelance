<div id="modalChangePassword" class="user-modal modal-authen @if(empty($from_reset)) hidden @endif">
    <div class="pop-up">
        <div class="row">
            <div class="col-md-6 col-md-push-3">
                <form action="{{ route('api.password.changePost') }}" method="POST" name="form_forgot_password" novalidate>
                    <div class="wrap-popup-card">
                        <a href="javascript:;" class="close"
                           data-dismiss="popup" data-target="#modalForgotPassword"><i class="icn-close"></i></a>
                        <div class="wp-card">
                            <div class="row modal-error-title">
                                <h5>@lang('passwords.set_new_passwords')</h5>
                            </div>
                            <div class="form-line">
                                <div class="form-input">
                                    <input type="password" class="password required" name="password" placeholder="@lang('passwords.placeholders.password')">
                                    <svg class="icon-eye right show-pass" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 12.5C1 12.5 5 4.16663 12 4.16663C19 4.16663 23 12.5 23 12.5C23 12.5 19 20.8333 12 20.8333C5 20.8333 1 12.5 1 12.5Z" stroke="#8F9FAF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 15.625C13.6569 15.625 15 14.2259 15 12.5C15 10.7741 13.6569 9.375 12 9.375C10.3431 9.375 9 10.7741 9 12.5C9 14.2259 10.3431 15.625 12 15.625Z" stroke="#8F9FAF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="form-input">
                                    <input type="password" class="password required" name="password_confirmation" placeholder="@lang('passwords.placeholders.password_confirmation')">
                                    <svg class="icon-eye right show-pass" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 12.5C1 12.5 5 4.16663 12 4.16663C19 4.16663 23 12.5 23 12.5C23 12.5 19 20.8333 12 20.8333C5 20.8333 1 12.5 1 12.5Z" stroke="#8F9FAF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 15.625C13.6569 15.625 15 14.2259 15 12.5C15 10.7741 13.6569 9.375 12 9.375C10.3431 9.375 9 10.7741 9 12.5C9 14.2259 10.3431 15.625 12 15.625Z" stroke="#8F9FAF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>

                                <input type="hidden" name="token" value="{{!empty($token)?$token:''}}" />
                                <input type="hidden" name="email" value="{{!empty($email)?$email:''}}" />
                            </div>
                            <button type="submit" class="btn btn-modal btn-recover-password">@lang('passwords.set_password')</button>
                            <div class="error" style="margin-top: 20px;"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(function () {
            var modalChangePassword = $('#modalChangePassword');
            var formChangePassword = modalChangePassword.find('form');
            var errorContainer = modalChangePassword.find('.error');

            formChangePassword.on('submit', function (e) {
                e.preventDefault();

                // Hide error
                formChangePassword.removeClass('invalid');
                errorContainer.text('');

                // Get roles by workspace
                $.ajax({
                    type: formChangePassword.attr('method'),
                    url: formChangePassword.attr('action'),
                    headers: {
                        'X-CSRF-TOKEN': '{!! csrf_token() !!}'
                    },
                    data: formChangePassword.serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            modalChangePassword.addClass('hidden')
                            $('#modalChangePasswordConfirmation').removeClass('hidden')
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (error) {
                        var response = error.responseJSON;

                        // Show error
                        formChangePassword.addClass('invalid');
                        errorContainer.text(response.message);
                    },
                });

                return false;
            });
        });
    </script>
@endpush