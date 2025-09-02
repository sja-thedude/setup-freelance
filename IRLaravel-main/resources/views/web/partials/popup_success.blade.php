<div id="modalChangePasswordConfirmation" class="user-modal hidden modal-authen">
    <div class="bg"></div>
    <div class="pop-up">
        <div class="row">
            <div class="col-md-6 col-md-push-3">
                <div class="wrap-popup-card">
                    <a href="javascript:;" class="close"><i class="icn-close"></i></a>
                    <div class="wp-card">
                        <div class="row modal-error-title">
                            <h5>@lang('auth.forgot_password_modal.password_changed')</h5>
                        </div>
                        <div class="row">
                            <img width="106" height="91" src="{!! url("/images/home/icon-success-order.svg") !!}">
                            <span class="modal-error-content">@lang('passwords.reset')</span>
                        </div>
                        <button type="button" class="btn btn-modal btn-show-login-modal" data-toggle="popup" data-target="#modalLogin" style="line-height: normal;">@lang('auth.login')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Welcome new user -->
<div id="modalWelcomeNewUser" class="user-modal @if(empty($from_register)) hidden @endif">
    <div class="bg"></div>
    <div class="pop-up">
        <div class="row">
            <div class="col-md-6 col-md-push-3">
                <div class="wrap-popup-card">
                    <a href="javascript:;" class="close"><i class="icn-close"></i></a>
                    <div class="wp-card">
                        <div class="row modal-error-title">
                            <h5>@lang('manager.send_invitation_subject')</h5>
                        </div>
                        <div class="row">
                            <img width="106" height="91" src="{!! url("/images/home/icon-success-order.svg") !!}">
                            <span class="modal-error-content">@lang('auth.message_verified_successfully')</span>
                        </div>
                        <button type="button" id="auto-login" data-url="{{route('register.autoLogin')}}" data-token="{{!empty($verify_token)?$verify_token:''}}" class="btn btn-modal" style="line-height: normal;">@lang('order.ready')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>