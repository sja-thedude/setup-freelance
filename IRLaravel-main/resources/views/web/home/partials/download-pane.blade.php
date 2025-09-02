<div class="row">
    <div class="col-xs-12 col-md-8">
        <div class="qr-code">
            <img src="{{asset('images/home/qr_code.svg')}}" />
        </div>
        <h3>{{trans('dashboard.download_app_title')}}</h3>
        <h5>{{trans('dashboard.order_eat_repeat')}}</h5>
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <a class="app-store">
                    <img src="{{ asset('images/home/appstore_badge_white.svg') }}" />
                </a>
                <a class="google-play">
                    <img src="{{ asset('images/home/google-play_white.svg') }}" />
                </a>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-4 img-download">
        <div class="img-phone">
            <div class="order-bell">
                <img src="{{asset('images/home/order-bell.svg')}}" />
            </div>
    
            <div class="iphoneX">
                <img src="{{asset('images/home/iphoneX.png')}}" />
            </div>
        </div>
    </div>

</div>