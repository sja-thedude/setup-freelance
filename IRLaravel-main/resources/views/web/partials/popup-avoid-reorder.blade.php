<div class="user-modal modelGroupInactive">
    <div class="pop-up">
        <div class="row">
            <div class="col-md-6 col-md-push-3">
                <div class="wrap-popup-card">
                    <a href="javascript:;" class="close"><i class="icn-close"></i></a>
                    <div class="wp-card">
                        <div class="row modal-error-title">
                            <h5>@lang('cart.oeps')</h5>
                        </div>
                        <div class="row modal-error-content">
                            <span>
                                {{trans('group.inactive')}}
                            </span>
                        </div>
                        <a href="javascript:;"
                           onclick="document.getElementsByClassName('modelGroupInactive')[0].classList.add('hidden')"
                           class="btn btn-modal btn-modal-error">@lang('cart.teruq')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>