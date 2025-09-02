@php
    $now = \Carbon\Carbon::now(config('app.timezone_fixed'))->format('d');
    $startTime = \Carbon\Carbon::parse(request()->range_start_date)->format('d');
    $endTime = \Carbon\Carbon::parse(request()->range_end_date)->format('d');
    $dateTimeCompare = ($now == $startTime && $now == $endTime) ? 'hidden' : 'visible';
    $jsDateFormat = config('datetime.jsDateFormat');

    if ($order->isBuyYourSelf() && empty(request('date_range'))) {
        // only show the time because the date is no longer relevant since it's an in-house order.
        $jsDateFormat = '';
    }

    if (empty($order->payed_at)){
        // Group order or Order table ordering
        if (!empty($order->group_id) || $order->isTableOrdering()){
            $lastPaidOrder = $order->getLastPaidChildOrder();
            $order->payed_at = $lastPaidOrder->payed_at ?? null;
        }
    }
@endphp

<div class="row-main-content min-h-62 col-sm-12 col-xs-12">
    <div class="{!! $order->print_class !!}"></div>
    <div class="row">
        <div class="col-item has-expand col-sm-2 col-xs-12">
            @if($order->type === \App\Helpers\OrderHelper::TYPE_IN_HOUSE)
                <svg class="pull-left order-group-icon mgr-10" width="27" height="35" viewBox="0 0 27 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.1328 35H9.86719V6.78125H0.5625V0.734375H26.4375V6.78125H17.1328V35Z" fill="black" fill-opacity="0.1"/>
                </svg>
            @elseif($order->type === \App\Helpers\OrderHelper::TYPE_SELF_ORDERING)
                <svg class="pull-left order-group-icon mgr-10" width="27" height="35" viewBox="0 0 27 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M28.4375 35H20.1875L11.2109 20.5625L8.14062 22.7656V35H0.875V0.734375H8.14062V16.4141L11 12.3828L20.2812 0.734375H28.3438L16.3906 15.8984L28.4375 35Z" fill="black" fill-opacity="0.1"/>
                </svg>
            @elseif(!empty($order->group_id))
                <svg class="pull-left order-group-icon mgr-10" width="29" height="36" viewBox="0 0 29 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15.3203 15.7578H28.9141V33.5234C26.7109 34.2422 24.6328 34.7422 22.6797 35.0234C20.7422 35.3203 18.7578 35.4688 16.7266 35.4688C11.5547 35.4688 7.60156 33.9531 4.86719 30.9219C2.14844 27.875 0.789062 23.5078 0.789062 17.8203C0.789062 12.2891 2.36719 7.97656 5.52344 4.88281C8.69531 1.78906 13.0859 0.242188 18.6953 0.242188C22.2109 0.242188 25.6016 0.945312 28.8672 2.35156L26.4531 8.16406C23.9531 6.91406 21.3516 6.28906 18.6484 6.28906C15.5078 6.28906 12.9922 7.34375 11.1016 9.45312C9.21094 11.5625 8.26562 14.3984 8.26562 17.9609C8.26562 21.6797 9.02344 24.5234 10.5391 26.4922C12.0703 28.4453 14.2891 29.4219 17.1953 29.4219C18.7109 29.4219 20.25 29.2656 21.8125 28.9531V21.8047H15.3203V15.7578Z" fill="black" fill-opacity="0.1"/>
                </svg>
            @endif
            
            <div class="pull-left time-convert order mgt--5"
                 data-format="{{ $jsDateFormat }}"
                 data-timeformat="{{ config('datetime.jsTimeShortFormat') }}"
                 data-datetime="{{ $order->gereed }}" data-showtime="{{ $dateTimeCompare }}">
            </div>
        </div>
        <div class="col-item has-expand col-sm-2 col-xs-12">
            @if(!empty($order->group_id))
                <strong class="pull-left">{!! $order->client_name !!}</strong>
                @if($order->isBuyYourSelf())
                    <span class="pull-left bubble-paid-status" style="background-color: {{ $order->checkOrderIsPaid() ? 'green' : 'red' }};"></span>
                @endif
            @elseif(!empty($order->type) && $order->isTableOrdering())
                <strong class="pull-left">{!! $order->client_name !!}</strong>
                <span class="pull-left bubble-paid-status" style="background-color: {{ $order->checkOrderIsPaidTableOrderingCash() ? 'green' : 'red' }};"></span>
            @else
                {!! $order->client_name !!}
            @endif
        </div>
        <div class="col-item has-expand col-sm-2 col-xs-12">
            @if ($order->payment_method == \App\Models\SettingPayment::TYPE_MOLLIE &&
                $order->status == \App\Models\Order::PAYMENT_STATUS_PAID && $order->payed_at)
                {!! $order->payment_method_show !!}:
                <span class="time-convert"
                      data-format="{!! config('datetime.jsDateTimeShortFormat') !!}"
                      data-datetime="{!! $order->payed_at !!}"></span>
            @else
                {!! $order->payment_method_show !!}
            @endif
        </div>
        <div class="col-item has-expand col-sm-1 col-xs-12 text-center">
            @if(empty($order->group_id))
                @if(!empty($order->printed_sticker))
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0)">
                            <path d="M15.0003 5L5.83366 14.1667L1.66699 10" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M19.3333 5L10.1667 14.1667L6 10" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <defs>
                            <clipPath id="clip0">
                                <rect width="20" height="20" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>
                @endif
            @else
                @if(!empty($order->printed_sticker))
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0)">
                            <path d="M15.0003 5L5.83366 14.1667L1.66699 10" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M19.3333 5L10.1667 14.1667L6 10" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <defs>
                            <clipPath id="clip0">
                                <rect width="20" height="20" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>
                @else
                    @php
                        $checkSubOrderSticker = $order->groupOrders()->where('printed_sticker', true)->first();
                    @endphp

                    @if(!empty($checkSubOrderSticker))
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16.3333 5L7.16667 14.1667L3 10" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    @endif
                @endif
            @endif
        </div>
        <div class="col-item has-expand col-sm-1 col-xs-12">
            {!! $order->transform_type !!}
        </div>
        <div class="col-item has-expand col-sm-2 col-xs-12">
            <span class="time-convert"
                  data-format="{!! config('datetime.jsDateTimeShortFormat') !!}"
                  data-datetime="{!! $order->beste_id !!}">
            </span>
            
            @if(!empty($order->meta_data))
                <span style="color: #26B99A; font-weight: bold" class="text-uppercase">App</span>
            @else
                <span style="color: #337ab7; font-weight: bold" class="text-uppercase">Web</span>
            @endif
        </div>
        <div class="col-item col-sm-2 col-xs-12">
            <div class="pull-left has-expand">
                {!! $order->daily_id_display !!}
            </div>
            <div class="pull-right text-right table_actions">
                @if($order->isTableOrdering() && !$order->hasLastPersonInTableOrdering())
                    @include('manager.orders.partials.actions.table_ordering.table_last_person_0')
                @else
                    @include('manager.orders.partials.table_item_actions', ['isShowSticker' => $isShowSticker])
                @endif
            </div>
        </div>
    </div>
</div>