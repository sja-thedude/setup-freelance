<div class="user-modal modal-fill-address modelProductAvaliable">
    <div class="pop-up">
        <div class="row">
            <div class="col-md-6 col-md-push-3">
                <div class="wrap-popup-card">
                    <a href="javascript:;" @if (isset($url)) onclick="window.location='{{ $url }}'" @endif class="close">
                        <i class="icn-close"></i>
                    </a>
                    <div class="wp-card">
                        <div class="row modal-error-title">
                            <h5>@lang('cart.oeps')</h5>
                        </div>
                        <div class="row modal-error-content">
                            <span>
                                {!! $message !!}
                            </span>
                        </div>
                        <a href="javascript:;"
                           @if (isset($url))
                                onclick="window.location='{{ $url }}'"
                           @else
                                onclick="document.getElementsByClassName('modelProductAvaliable')[0].classList.add('hidden')"
                           @endif
                           class="btn btn-modal btn-modal-error btn-pr-custom">@lang('cart.teruq')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>