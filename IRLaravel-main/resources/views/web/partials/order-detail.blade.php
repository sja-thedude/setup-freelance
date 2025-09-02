<div class="row">
    <div class="col-md-8 col-md-push-2">
        <div class="wrap-popup-order pc-wrap">
            <a href="javascript:;" class="close" data-dismiss="popup" data-target="#pop-order-detail">
                <i class="icn-close"></i>
            </a>
            <div class="row wrap-andere-box equal-height ">
                <div class="col-md-6 ">
                    <div class="andere-box item">
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
                            {{--<div class="row-table">--}}
                            {{--<div class="col-left">--}}
                            {{--<h6>@lang('cart.success_type_bestelling'):</h6>--}}
                            {{--</div>--}}
                            {{--<div class="col-right">--}}
                            {{--@if($order->type === 0)--}}
                            {{--<h6>@lang('cart.success_afhaal')</h6>--}}
                            {{--@endif--}}
                            {{--</div>--}}
                            {{--</div>--}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 item" id="pop-parentCart">
                    <div class="andere-box item">
                        @if ($order && isset($order->orderItems) && $order->orderItems->count() > 0)
                            @include('web.carts.partials.popup-order-detail', [
                                'isSuccessPage'         => TRUE,
                                'cart'                  => $order,
                                'listItem'              => $order->orderItems,
                            ])
                        @endif
                    </div>
                </div>
            </div>

            @if(!Request::get('main_system', false))
            <div class="row">
                <div class="col-md-12 text-center">
                    {!! Form::open(['route' => [$guard.'.carts.orderAgain', $order->id], 'name' => 'orderAgain', 'method' => 'POST', 'class' => 'order-again-now']) !!}
                    {!! Form::close() !!}
                    <a href="javascript:;" class="btn btn-andere show-popup btn-pr-custom" data-target="pop-search-address"
                       data-order-type="{{$order->type}}"
                       data-group="{{$order->parent_id ? 'true' : 'false'}}">
                        @lang('frontend.order_again')
                    </a>
                </div>
            </div>
            @endif
        </div>

        <div class="wrap-popup-order mobile-wrap">
            <a href="javascript:;" class="close" data-dismiss="popup" data-target="#pop-order-detail">
                <i class="icn-close"></i>
            </a>
            <div class="order-history-slider owl-carousel" style="display: block">
                <div class="andere-box item">
                    @if ($order && isset($order->orderItems) && $order->orderItems->count() > 0)
                    @include('web.carts.partials.popup-order-detail', [
                        'isSuccessPage'         => TRUE,
                        'cart'                  => $order,
                        'listItem'              => $order->orderItems,
                        ])
                    @endif
                </div>
                <div class="andere-box item">
                    <h6>
                        @if ($order->group_id)
                            @lang('cart.success_id_group') #G{{ $order->parent_code . (!empty($order->group_id) && !empty($order->extra_code) ? '-' . $order->extra_code : '') }}
                        @else
                            @lang('cart.success_id') #{{ $order->code }}
                        @endif
                    </h6>
                    <div class="name-restaurant display-none">
                        {!! !empty($order->workspace) ? $order->workspace->name : '' !!}
                    </div>
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
            @if(!Request::get('main_system', false))
                <div class="row">
                    <div class="col-md-12 text-center">
                        {!! Form::open(['route' => [$guard.'.carts.orderAgain', $order->id], 'name' => 'orderAgain', 'method' => 'POST', 'class' => 'order-again-now']) !!}
                        {!! Form::close() !!}
                        <a href="javascript:;" class="btn btn-andere show-popup" data-target="pop-search-address"
                        data-order-type="{{$order->type}}"
                        data-group="{{$order->parent_id ? 'true' : 'false'}}">
                            @lang('frontend.order_again')
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@include('web.partials.popup-search-address')

<script>
    $(document).ready(function(){
        $(".order-history-slider").owlCarousel({
            loop: false,
            nav: false,
            dots: true,
            responsive: {
                0: {
                    items: 1,
                },
                600: {
                    items: 1,
                },
            }
        });
    });
</script>