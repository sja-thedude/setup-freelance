<ul>
    {{-- @include('web.partials.menu-language', ['class' => ''])--}}
    <li>
        <a href="javascript:;" class="header-time has-submenu">
            <i class="icn-time"></i>
        </a>
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
                    <a>@lang('frontend.bestelling') #{{$order->daily_id_display . (!empty($order->group_id) && !empty($order->extra_code) ? '-' . $order->extra_code : '')}}</a>
                    <span>
                        <?php
                            $dateTimeLocal = \App\Helpers\Helper::convertDateTimeToTimezone($order->date_time, $order->timezone);
                        ?>
                        {{Carbon\Carbon::parse($dateTimeLocal)->format(config('datetime.dateFormat'))}}
                    </span>
                    <span>
                        €{{\App\Helpers\Helper::formatPrice($order->total_price)}} -
                        @if($order->type == \App\Models\Cart::TYPE_TAKEOUT)
                            @lang('cart.tab_afhaal')
                        @elseif($order->type == \App\Models\Cart::TYPE_LEVERING)
                            @lang('cart.tab_levering')
                        @else
                            @lang('cart.tab_in_house')
                        @endif
                    </span>
                    <a href="javascript:;" class="btn btn-andere order-detail"
                       data-target="pop-order-detail"
                       data-route="{!! route($guard.'.orders.show', [$order->id]) !!}"
                       data-toggle="popup"
                       data-target="#pop-order-detail">
                        @lang('frontend.bekijk')
                    </a>
                </li>
                @endforeach
            </ul>
        @else
        <ul class="sub-menu sub-menu-email sub-order-history">
            <div class="no-order-available">
                <span>@lang('notification.no_order')</span>
            </div>
        </ul>
        @endif
    </li>
    <li>
        <a href="javascript:void(0);" class="header-email has-submenu notifications"
            data-route="{!! route($guard.'.notification.index') !!}">
            <i class="icn-email"></i>

            @if(!auth()->guest())
                <span class="number user-{{auth()->user()->id}}">
                    {{Helper::displayNotificationNumberByUser()}}
                </span>
            @endif
        </a>
        @if(!auth()->guest())
            <ul id="notification-list" class="sub-menu sub-menu-email"></ul>
        @endif
    </li>

    @if(!auth()->guest())
        @include('web.partials.menu-profile')
    @endif
</ul>