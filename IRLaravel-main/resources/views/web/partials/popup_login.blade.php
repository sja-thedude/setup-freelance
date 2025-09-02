{{--
Add HTML classs "btn-show-login-modal" to show Login modal
--}}

<div id="modalLogin" class="user-modal hidden">
    <div class="bg"></div>
    <div class="pop-up">
        <div class="row">
            <div class="col-md-6 col-md-push-3">
                <form action="{{ route('api.submit_login') }}" method="POST" name="form_login" novalidate>
                    <div class="wrap-popup-card">
                        <a href="javascript:;" class="close"
                            data-dismiss="popup" data-target="#modalLogin"><i class="icn-close"></i></a>
                        <div class="wp-card">
                            <div class="row modal-error-title">
                                <h5>@lang('auth.login_modal.title')</h5>
                            </div>
                            <div class="row">
                                <span class="modal-error-content mb-0">@lang('auth.login_modal.description')</span>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="modal-login-container">
                                         <div class="custom-form-input">
                                             <input type="email" name="email" class="username required"  placeholder="@lang('auth.email')">
                                         </div>
                                        <div class="custom-form-input">
                                            <input type="password" name="password" class="password required"  placeholder="@lang('auth.password')">
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
                                        <div class="custom-form-input">
                                            <div class="error" style="margin-bottom: 20px;"></div>
                                        </div>
                                        <div class="custom-form-input">
                                            <button type="submit" class="btn btn-modal btn-login btn-disable btn-pr-custom" disabled>@lang('auth.button_login')</button>
                                            <a href="javascript:void(0);" class="last btn-show-forgot-password-modal"
                                                data-toggle="popup" data-target="#modalForgotPassword">@lang('auth.forgot_password')</a>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12">
                                                @include('auth.social_login', [
                                                    'site' => 'login_with',
                                                    'horizontal' => true
                                                ])
                                            </div>
                                        </div>
                                    </div>
                                    <div class="register-container mt-0 mb-10">
                                        <a href="javascript:void(0);" class="btn btn-modal btn-register btn-show-register-modal"
                                            data-toggle="popup" data-target="#modalRegister">@lang('auth.login_modal.button_register')</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Register popup --}}
@include('web.partials.popup_register')

{{-- Forgot Password popup --}}
@include('web.partials.popup_forgot_password')

@push('scripts')
    <script>
        $(function () {
            var modalLogin = $('#modalLogin');
            var formLogin = modalLogin.find('form[name="form_login"]');
            var errorContainer = modalLogin.find('.error');

            // Btn show login modal
            var btnShowLoginModal = $('.btn-show-login-modal');

            // Show login modal
            btnShowLoginModal.on('click', function (e) {
                e.preventDefault();

                $('#modalRegister, #modalForgotPassword').addClass('hidden');
                $('#modalLogin').removeClass('hidden');

                // return false;
            });

            formLogin.on('submit', function (e) {
                e.preventDefault();

                // Hide error
                formLogin.removeClass('invalid');
                errorContainer.text('');

                // Get roles by workspace
                $.ajax({
                    type: formLogin.attr('method'),
                    url: formLogin.attr('action'),
                    headers: {
                        'X-CSRF-TOKEN': '{!! csrf_token() !!}'
                    },
                    data: formLogin.serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            var data = response.data;

                            // Update cart when has login
                            $.ajax({
                                type: "POST",
                                url: "{{ route('web.cart.storeWithoutLogin') }}",
                                headers: {
                                    'X-CSRF-TOKEN': '{!! csrf_token() !!}'
                                },
                                data: {workspaceId: "{{ $webWorkspace->id }}", userId: data.id},
                                dataType: 'json',
                                success: function (response) {
                                    if (response.code === 200) {
                                        window.location.href = window.DOMAIN + '/auth/token/' + data.token + '?redirect=' + window.location.href;
                                    }
                                },
                                error: function (error) {
                                    console.log(error);
                                },
                            });

                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (error) {
                        var response = error.responseJSON;

                        // Show error
                        formLogin.addClass('invalid');
                        errorContainer.text(response.message);
                    },
                });

                return false;
            });
        });
    </script>
@endpush