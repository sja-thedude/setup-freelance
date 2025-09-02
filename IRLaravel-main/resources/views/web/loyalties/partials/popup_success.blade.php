<div id="modalRedeemSuccess" class="pop-up" style="display: none;">
    <div class="row">
        <div class="col-md-6 col-md-push-3">
            <div class="wrap-popup-card">
                <a href="javascript:;" class="close" style="z-index: 9999;"><i class="icn-close"></i></a>
                <div class="wp-card">

                    {{-- Content for physical reward --}}
                    @include('web.loyalties.partials.popup_success.discount')

                    {{-- Content for gift reward --}}
                    @include('web.loyalties.partials.popup_success.gift')

                </div>
            </div>
        </div>
    </div>
</div>