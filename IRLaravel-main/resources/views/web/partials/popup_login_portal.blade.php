{{--
Add HTML classs "btn-show-login-modal" to show Login modal
--}}

<div id="modalLogin" class="user-modal hidden modal-authen portal-login">
    <div class="bg"></div>
    <div class="pop-up">
        <div class="row">
            <div class="col-md-6 col-md-push-3">
                <form action="{{ route('api.submit_login') }}" class="form-horizontal form-login" method="POST" name="form_login" novalidate>
                    <div class="wrap-popup-card">
                        <a href="javascript:;" class="close"
                            data-dismiss="popup" data-target="#modalLogin"><i class="icn-close"></i></a>
                        <div class="wp-card">
                            <div class="row modal-error-title">
                                <h5>@lang('strings.login')</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="modal-login-container">
                                        <div class="form-group ip-has-icon">
                                            <svg class="first-icon" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M16.9333 17.4001C16.6593 17.5877 16.3318 17.6813 16 17.6668C15.6682 17.6813 15.3407 17.5877 15.0667 17.4001L8 13.6001V20.3334C8 21.438 8.89543 22.3334 10 22.3334H22C23.1046 22.3334 24 21.438 24 20.3334V13.6001L16.9333 17.4001Z" fill="#8898AA"/>
                                                <path d="M22 9H10C8.89544 9 8.00001 9.89543 8.00001 11V11.6667C7.99871 11.9112 8.12507 12.1386 8.33334 12.2667L15.6667 16.2667C15.769 16.3206 15.8848 16.3437 16 16.3333C16.1152 16.3437 16.231 16.3206 16.3333 16.2667L23.6667 12.2667C23.8749 12.1386 24.0013 11.9112 24 11.6667V11C24 9.89543 23.1046 9 22 9Z" fill="#8898AA"/>
                                            </svg>
                                            <input id="email" type="email" class="form-control ir-login-input required" name="email" value="{{ old('email') }}"
                                                   placeholder="@lang('auth.email')">
                                        </div>

                                        <div class="form-group ip-has-icon">
                                            <svg class="first-icon" width="32" height="32" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.625 9.40269C9.47169 8.91106 10.4521 8.625 11.5 8.625C14.6704 8.625 17.25 11.2046 17.25 14.375C17.25 17.5454 14.6704 20.125 11.5 20.125C8.32959 20.125 5.75 17.5454 5.75 14.375C5.75 12.9224 6.29553 11.5977 7.1875 10.5843V7.1875C7.1875 4.80916 9.12166 2.875 11.5 2.875C13.8783 2.875 15.8125 4.80916 15.8125 7.1875H14.375C14.375 5.60194 13.0856 4.3125 11.5 4.3125C9.91444 4.3125 8.625 5.60194 8.625 7.1875V9.40269ZM12.2188 17.25V15.6803C13.0532 15.3827 13.6562 14.5921 13.6562 13.6562C13.6562 12.4674 12.6888 11.5 11.5 11.5C10.3112 11.5 9.34375 12.4674 9.34375 13.6562C9.34375 14.5921 9.94678 15.3827 10.7812 15.6803V17.25H12.2188Z" fill="#8898AA"/>
                                            </svg>
                                            <input id="password" type="password" class="form-control ir-login-input password required" name="password"
                                                   placeholder="@lang('auth.password')">
                                            <svg class="icon-eye right show-pass" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 12.5C1 12.5 5 4.16663 12 4.16663C19 4.16663 23 12.5 23 12.5C23 12.5 19 20.8333 12 20.8333C5 20.8333 1 12.5 1 12.5Z" stroke="#8F9FAF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M12 15.625C13.6569 15.625 15 14.2259 15 12.5C15 10.7741 13.6569 9.375 12 9.375C10.3431 9.375 9 10.7741 9 12.5C9 14.2259 10.3431 15.625 12 15.625Z" stroke="#8F9FAF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <div class="form-group">
                                            <label class="container-cb inline-block">
                                                <input type="checkbox" class="form-control" value="1" name="remember"
                                                        {{ old('remember') ? 'checked' : '' }}/>
                                                <span class="checkmark"></span>
                                                <span>@lang('auth.remember')</span>
                                            </label>
                                            <a href="javascript:void(0);" class="last btn-show-forgot-password-modal forgot-password" data-toggle="popup" data-target="#modalForgotPassword">@lang('auth.forgot_password')</a>
                                        </div>

                                        <div class="form-group mgb-0 text-center form-btn">
                                            <button type="submit" class="btn btn-primary btn-login" disabled>
                                                @lang('auth.login_v2')
                                            </button>

                                            <button class="btn btn-register btn-show-register-modal" data-toggle="popup" data-target="#modalRegister">
                                                @lang('auth.button_register')
                                            </button>

                                            <div class="row mt-20">
                                                <div class="col-sm-12 col-xs-12">
                                                    @include('auth.social_login', [
                                                        'site' => 'login_with',
                                                        'horizontal' => true
                                                    ])
                                                </div>
                                            </div>
                                        </div>
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
@include('web.partials.popup_register_portal')

{{-- Forgot Password popup --}}
@include('web.partials.popup_forgot_password_portal')

@include('web.partials.popup_changepwd')

@include('web.partials.popup_success')

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
                                data: {workspaceId: "{{ !empty($webWorkspace)?$webWorkspace->id:'' }}", userId: data.id},
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