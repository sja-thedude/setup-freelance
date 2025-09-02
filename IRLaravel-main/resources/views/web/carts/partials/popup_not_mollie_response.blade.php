@if(session()->has('not_response_mollie'))
    <div id="modalLogin" class="user-modal modal-authen">
        <div class="bg"></div>
        <div class="pop-up">
            <div class="row">
                <div class="col-md-6 col-md-push-3">
                    <div class="form-horizontal form-login">
                        <div class="wrap-popup-card">
                            <a href="javascript:;" class="close" data-dismiss="popup" data-target="#modalLogin">
                                <i class="icn-close"></i>
                            </a>
                            <div class="wp-card">
                                <h6 style="margin-top: 30px;">@lang('cart.not_response_mollie')</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        \Session::forget('not_response_mollie');
    @endphp
@endif