<h2>@lang('frontend.order_history')</h2>
<ul>
    <li>
        @php
            $inStatuses = [
                \App\Models\Order::PAYMENT_STATUS_PENDING,
                \App\Models\Order::PAYMENT_STATUS_PAID,
            ];
            $orderLists = \App\Helpers\Order::getOrderByUser($inStatuses);
        @endphp
        @if(!$orderLists->isEmpty())
            <ul class="sub-menu sub-menu-email sub-menu-time sub-order-history">
                @foreach($orderLists as $order)
                    @php
                        $order = OrderHelper::convertOrderItem($order);
                    @endphp
                <li @if(count($orderLists) == 1) class="border-none" @endif>
                    <div>
                        <p>
                            <?php
                                $dateTimeLocal = \App\Helpers\Helper::convertDateTimeToTimezone($order->date_time, $order->timezone);
                            ?>
                            @lang('cart.success_datetime', [
                                'date' => Carbon\Carbon::parse($dateTimeLocal)->format('d/m/Y'),
                                'time' => Carbon\Carbon::parse($dateTimeLocal)->format('H:i')
                            ])
                        </p>
                        <a>{!! !empty($order->workspace) ? $order->workspace->name : lang('frontend.bestelling') !!}</a>
                        <span>
                            - #{{$order->daily_id_display . (!empty($order->group_id) && !empty($order->extra_code) ? '-' . $order->extra_code : '')}}
                        </span>
                        <span>
                           - €{{\App\Helpers\Helper::formatPrice($order->total_price)}} -
                            @if($order->type == \App\Models\Cart::TYPE_TAKEOUT)
                                @lang('cart.tab_afhaal')
                            @elseif($order->type == \App\Models\Cart::TYPE_LEVERING)
                                @lang('cart.tab_levering')
                            @else
                                @lang('cart.tab_in_house')
                            @endif
                        </span>
                    </div>
                    <div class="eye-icon">
                        <a href="javascript:;" class="eye-color-portal order-detail"
                           data-target="pop-order-detail"
                           data-route="{!! route($guard.'.orders.show', [$order->id]) !!}"
                           data-toggle="popup"
                           data-target="#pop-order-detail">
                           <i class="icn-eye-color"></i>
                        </a>
                    </div>
                </li>
                @endforeach
            </ul>
        @endif
    </li>
</ul>