@php
    $currency = '€';
    $now = \Carbon\Carbon::now(config('app.timezone_fixed'))->format('d');
    $startTime = \Carbon\Carbon::parse(request()->range_start_date)->format('d');
    $endTime = \Carbon\Carbon::parse(request()->range_end_date)->format('d');
    $dateTimeCompare = ($now == $startTime && $now == $endTime) ? 'hidden' : 'visible';
@endphp
<div class="list-responsive">
    <div class="list-header">
        <div class="row">
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('id')}}">
                    @lang('order.order_id') {{Helper::getIconSort('id')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('workspace_id')}}">
                    @lang('order.restaurant_source_name') {{Helper::getIconSort('workspace_id')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('date_time')}}">
                    @lang('order.gereed') {{Helper::getIconSort('date_time')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                @lang('order.printing_status')
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('updated_at')}}">
                    @lang('order.order_creation') {{Helper::getIconSort('updated_at')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12 text-capitalize">
                @lang('order.payment_method')
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                @lang('order.customer_information')
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                @lang('order.total_order_value')
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                @lang('order.type')
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                @lang('common.actions')
            </div>
        </div>
    </div>
    <div class="list-body restaurant">
        @foreach($orders as $order)
            @php
                $isShowSticker = !empty($order->workspace->workspaceExtras) ? $order->workspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::STICKER)->first() : null;
                $jsDateFormat = config('datetime.jsDateFormat');

                if ($order->isBuyYourSelf() && empty(request('date_range'))) {
                    // only show the time because the date is no longer relevant since it's an in-house order.
                    $jsDateFormat = config('datetime.jsDateTimeFormat');
                }

                if (empty($order->payed_at)){
                    // Group order or Order table ordering
                    if (!empty($order->group_id) || $order->isTableOrdering()){
                        $lastPaidOrder = $order->getLastPaidChildOrder();
                        $order->payed_at = $lastPaidOrder->payed_at ?? null;
                    }
                }
            @endphp
            <div id="tr-{{ $order->id }}" class="row">
                <div class="col-item col-sm-1 col-xs-12">
                    {!! $order->id !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    {!! !empty($order->workspace) ? $order->workspace->name : '' !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    <span class="time-convert"
                         data-format="{{ $jsDateFormat }}"
                         data-timeformat="{{ config('datetime.jsTimeShortFormat') }}"
                         data-datetime="{{ $order->gereed }}"
                         data-showtime="{{ $dateTimeCompare }}">
                    </span>
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    @if($order->print_class == 'print-status-success')
                        @lang('order.printed')
                    @elseif($order->print_class == 'print-status-normal')
                        @lang('order.to_be_printed')
                    @else
                        @lang('order.not_printed')
                    @endif
                </div>
                <div class="col-item col-sm-2 col-xs-12">
                    <span class="time-convert"
                          data-format="{!! config('datetime.jsDateTimeShortFormat') !!}"
                          data-datetime="{!! $order->created_at !!}">
                    </span>
                    -
                    <span class="time-convert"
                          data-format="{!! config('datetime.jsDateTimeShortFormat') !!}"
                          data-datetime="{!! $order->updated_at !!}">
                    </span>
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    @if($order->payment_method == \App\Models\SettingPayment::TYPE_MOLLIE &&
                        $order->status == \App\Models\Order::PAYMENT_STATUS_PAID)
                        {!! $order->payment_method_show !!}:
                        <span class="time-convert"
                              data-format="{!! config('datetime.jsDateTimeShortFormat') !!}"
                              data-datetime="{!! $order->payed_at !!}"></span>
                    @else
                        {!! $order->payment_method_show !!}
                    @endif
                </div>
                <div class="col-item col-sm-1 col-xs-12">
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
                <div class="col-item col-sm-1 col-xs-12">
                    {!! $currency.$order->calculate_total_price !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    {!! $order->transform_type !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12">
                    <div class="table_actions mgb-5">
                        @include($guard.'.orders.partials.table_item_actions', ['isShowSticker' => $isShowSticker])
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@if(!empty($orders))
    {{ $orders->appends(request()->except(['menu', '_token']))->links() }}
@endif