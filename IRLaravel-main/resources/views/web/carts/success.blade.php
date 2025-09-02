@extends('layouts.web-user')

@section('content')
    <div id="main-body">
        @include('layouts.partials.error-msg')

        {{--Create value redeem hidden to main.js calculate--}}
        <div class="wrap-box hidden" id="wrapRedeem" >
            <a href="javascript:;"
               data-discount="{{ $redeemDiscount }}"
               data-id="{{ $order->redeem_history_id }}" class="btn btn-andere"
            >@lang('cart.pas_toe')</a>
        </div>
        
        <div class="ap-content ap-success">
            <div class="row mb-50">
                <div class="col-md-4 col-md-push-4 text-center">
                    <img src="{{ url('/images/success.svg') }}" alt="success">
                    <h5>@lang('cart.success_title')</h5>
                    <p>@lang('cart.success_subtitle')</p>
                    <a href="{{ route('web.index') }}" class="btn btn-andere btn-pr-custom">@lang('cart.success_btn')</a>
                </div>
            </div>
            <div class="row wrap-andere-box">
                <div class="col-md-2 col-none"></div>
                <div class="col-md-4">
                    <div class="andere-box">
                        <h5>
                            @if ($order->group_id)
                                @lang('cart.success_id_group') #G{{ $order->parent_code . (!empty($order->group_id) && !empty($order->extra_code) ? '-' . $order->extra_code : '') }}
                            @else
                                @lang('cart.success_id') #{{ $order->code }}
                            @endif
                        </h5>
                        <div class="wp-table">
                            <div class="row-table table-header">
                                <div class="col-left">
                                    <h6>
                                        <?php
                                            $dateTimeLocal = \App\Helpers\Helper::convertDateTimeToTimezone($order->date_time, $order->timezone);
                                        ?>
                                        @lang('cart.success_datetime', [
                                            'date' => Carbon\Carbon::parse($dateTimeLocal)->format('d/m/Y'),
                                            'time' => Carbon\Carbon::parse($dateTimeLocal)->format('H:i')
                                        ])
                                    </h6>
                                </div>
                                <div class="col-right"></div>
                            </div>
                            <div class="row-table">
                                <div class="col-left">
                                    <h6>@lang('cart.success_betaalstatus'):</h6>
                                </div>
                                <div class="col-right">
                                    <h6>
                                        {{ $order->is_test_account == \App\Models\Order::IS_TRUST_ACCOUNT && ($order->payment_method == \App\Models\SettingPayment::TYPE_CASH || $order->payment_method == \App\Models\SettingPayment::TYPE_FACTUUR)
                                            ? trans('cart.success_te_betalen')
                                            : trans('cart.success_betaald')
                                        }}
                                    </h6>
                                </div>
                            </div>
                            <div class="row-table">
                                <div class="col-left">
                                    <h6>@lang('cart.success_betaalmethode'):</h6>
                                </div>
                                <div class="col-right">
                                    <h6>{{ \App\Models\SettingPayment::getTypes()[$order->payment_method] }}</h6>
                                </div>
                            </div>

                            @if ($order->note)
                                <div class="row-table">
                                    <div class="col-left">
                                        <h6>@lang('cart.success_opmerkingen'):</h6>
                                    </div>
                                    <div class="col-right">
                                        <h6>{{ $order->note }}</h6>
                                    </div>
                                </div>
                            @endif

                            @if($order->type === \App\Models\Cart::TYPE_TAKEOUT)
                                <div class="row-table">
                                    <div class="col-left">
                                        <h6>@lang('cart.success_type_bestelling'):</h6>
                                    </div>
                                    <div class="col-right">
                                        @if ($order->group_id)
                                            <h6>{{ $order->group->type == \App\Models\Cart::TYPE_TAKEOUT ? trans('cart.success_afhaal') : trans('cart.success_levering') }}</h6>
                                        @else
                                            <h6>@lang('cart.success_afhaal')</h6>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($order->type === \App\Models\Cart::TYPE_LEVERING)
                                <div class="row-table">
                                    <div class="col-left">
                                        <h6>@lang('cart.levering_op_adres'):</h6>
                                    </div>
                                    <div class="col-right">
                                        <h6>{{ $order->address }}</h6>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4 ">
                    <div class="parentCart">
                        <div class="wrap-sidebar andere-box">
                            @if ($order && isset($order->orderItems) && $order->orderItems->count() > 0)
                                @php
                                    $totalPrice = 0;
                                    if (session()->has('idsProductFail')) {
                                        $idsProductFail = session()->get('idsProductFail');
                                        session()->forget('idsProductFail');
                                    }
    
                                    $orderItems = $order->orderItems;
                                @endphp

                                @include('web.carts.partials.step1', [
                                    'isSuccessPage'         => TRUE,
                                    'cart'                  => $order,
                                    'priceDiscount'         => ($order->coupon_discount + $order->redeem_discount + $order->group_discount),
                                    'listItem'              => $order->orderItems,
                                    'isDeleveringAvailable' => $isDeleveringAvailable,
                                    'isDeleveringPriceMin'  => $isDeleveringPriceMin,
                                    'conditionDelevering'   => $conditionDelevering,
                                    'redeemId'              => $order->redeem_history_id,
                                    'totalCouponDiscount'   => $order->coupon_discount,
                                    'discountProducts'      => $discountProducts,
                                    'couponDiscount'        => $couponDiscount,
                                    'redeemDiscount'        => $redeemDiscount,
                                    'groupDiscount'         => $groupDiscount,
                                ])
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay"></div>
    </div>

    @if (request('is_api') == 1)
        @php($data = [
            'screen'   => 'payment_success',
            'order_id' => $order->id,
        ])

        <script>
            function showContent() {
                document.getElementsByTagName('body')[0].style.display = 'block';
            }

            /**
             * Deep Linking to Your Mobile App from Your Website
             * @link https://tune.docs.branch.io/sdk/deep-linking-to-your-mobile-app-from-your-website/
             */
            window.addEventListener('DOMContentLoaded', (event) => {
                // var confirm = window.confirm('Open in It’s Ready app');
                //
                // if (confirm) {
                var device = getMobileOperatingSystem();
                console.log('device:', device);

                if (device === 'Android') {
                    <!-- Deep link URL for existing users with app already installed on their device -->
                    window.location = '{{ array_get($config, 'android.deeplink') }}?{!! http_build_query($data) !!}';
                    <!-- Download URL (TUNE link) for new users to download the app -->
                    {{--setTimeout("window.location = '{{ config('mobile.android.download') }}';", 1000);--}}
                    setTimeout(function () {
                        showContent();
                    }, 1000);
                } else if (device === 'iOS') {
                    <!-- Deep link URL for existing users with app already installed on their device -->
                    window.location = '{{ array_get($config, 'ios.deeplink') }}?{!! http_build_query($data) !!}';
                    <!-- Download URL (TUNE link) for new users to download the app -->
                    {{--setTimeout("window.location = '{{ config('mobile.ios.download') }}';", 1000);--}}
                    setTimeout(function () {
                        showContent();
                    }, 1000);
                } else {
                    showContent();
                }
                // }

            });
        </script>
    @endif
@endsection
