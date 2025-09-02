@php
    $currency = '€';
@endphp

<div class="print-area print-a4-html" data-print_id="{!! $order->id !!}">
    <div class="print-main-content print-a4-content">
        @if(!empty($order->workspace) && !empty($order->workspace->name))
            <div class="row">
                <div class="col-sm-12 col-xs-12 text-center">
                    <div class="print-client-name">
                        {!! $order->workspace->name !!}
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-6 col-xs-6 text-left">
                @if(!empty($order->client_name))
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="print-client-name text-uppercase">
                                @if($order->is_test_account)
                                    ADMIN (@lang('order.test_order'))
                                @else
                                    {!! $order->client_name !!}
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="print-client-phone">
                            {!! $order->phone_display !!}
                            @if($order->type_convert == \App\Models\Order::TYPE_DELIVERY)
                                <p class="mgb-0 mgt-0">
                                    {!! $order->address_show !!}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xs-6 text-right">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="print-type text-uppercase">
                            {!! $order->transform_type !!}
                            -
                            <span>
                                {!! date('H:i', strtotime(Helper::convertDateTimeToTimezone($order->gereed, $timezone))) !!}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="print-type">
                            {!! date(config('datetime.dateFormat'), strtotime(Helper::convertDateTimeToTimezone($order->gereed, $timezone))) !!} - #{!! $order->daily_id_display !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="print-create-date">
                            @lang('order.print_a4_trans.print_create')
                            <span>
                                {!! date(config('datetime.timeFormat3'), strtotime(Helper::convertDateTimeToTimezone($order->beste_id, $timezone))) !!}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row print-title">
            <div class="col-sm-2 col-xs-2 text-uppercase">
                @lang('order.print_a4_trans.name')
            </div>
            <div class="col-sm-2 col-xs-2 text-uppercase">
                @lang('order.print_a4_trans.number')
            </div>
            <div class="col-sm-4 col-xs-4 text-uppercase">
                @lang('order.print_a4_trans.product')
            </div>
            <div class="col-sm-2 col-xs-2 text-uppercase text-right">
                @lang('order.print_a4_trans.unit')
            </div>
            <div class="col-sm-2 col-xs-2 text-uppercase text-right">
                @lang('order.print_a4_trans.total')
            </div>
        </div>

        @if(!empty($order->print_products))
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    @php
                        $orderHasNotes = [];
                    @endphp
                    @foreach($order->print_products as $printProduct)
                        @if(!empty($printProduct['category']) && !empty($printProduct['products']))
                            @php
                                $subOrder = $printProduct['order'];
                                $category = $printProduct['category'];
                                $products = $printProduct['products'];
                            @endphp

                            @if(!empty($subOrder->note) && !in_array($subOrder->id, $orderHasNotes))
                                @php
                                    $orderHasNotes[] = $subOrder->id;
                                @endphp
                                <hr class="dash-line"/>
                                <div class="row">
                                    <div class="col-sm-12 col-xs-12">
                                        <div class="print-note">
                                            <strong>{!! $subOrder->user->name !!}:</strong>
                                            {!! $subOrder->note !!}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @include('manager.orders.prints.partials.a4_product')
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <div class="row print-total-area">
            <div class="col-sm-8 col-xs-8">
                <div class="print-total">
                    @lang('order.order_items'): {!! $order->total_product_items !!}
                </div>
                @if(!empty($optionSettingId))
                    @if(!$option->items->isEmpty())
                        @foreach($option->items as $key => $optionItem)
                            <div class="print-total">
                                {!! $optionItem->name !!}:
                                {!! !empty($order->option_item_check[$optionItem->id]) ? $order->option_item_check[$optionItem->id] : 0 !!}
                            </div>
                        @endforeach
                    @endif
                @endif
            </div>

            <div class="col-sm-4 col-xs-4">
                <div class="row">
                    <div class="col-sm-12 col-xs-12 text-uppercase order-print-pay">
                        @php
                            $subTotal = $order->calculate_total_price;

                            if($order->type_convert == \App\Models\Order::TYPE_DELIVERY) {
                                $subTotal = !empty($subTotal - $order->calculate_ship_price) ? $subTotal - $order->calculate_ship_price : 0;
                            }
                            if(!empty($order->calculate_service_cost)) {
                                $subTotal = !empty($subTotal - $order->calculate_service_cost) ? $subTotal - $order->calculate_service_cost : 0;
                            }
                        @endphp

                        @if($order->type_convert == \App\Models\Order::TYPE_DELIVERY || !empty($order->calculate_service_cost))
                            <div class="row">
                                <div class="col-sm-6 col-xs-6 text-right">
                                    <strong>@lang('order.sub_total')</strong>
                                </div>
                                <div class="col-sm-6 col-xs-6 text-right">
                                    <strong>{!! $currency.number_format((float)($subTotal), 2, '.', '') !!}</strong>
                                </div>
                            </div>
                        @endif

                        @if($order->type_convert == \App\Models\Order::TYPE_DELIVERY)
                            <div class="row">
                                <div class="col-sm-6 col-xs-6 text-right">
                                    <strong>@lang('order.ship_price')</strong>
                                </div>
                                <div class="col-sm-6 col-xs-6 text-right">
                                    <strong>{!! $currency.number_format((float)(!empty($order->calculate_ship_price) ? $order->calculate_ship_price : 0), 2, '.', '') !!}</strong>
                                </div>
                            </div>
                        @endif

                        @if(!empty($order->calculate_service_cost))
                            <div class="row">
                                <div class="col-sm-6 col-xs-6 text-right">
                                    <strong>@lang('workspace.service_cost')</strong>
                                </div>
                                <div class="col-sm-6 col-xs-6 text-right">
                                    <strong>{!! $currency.number_format((float)(!empty($order->calculate_service_cost) ? $order->calculate_service_cost : 0), 2, '.', '') !!}</strong>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-sm-6 col-xs-6 text-right">
                                <strong>@lang('order.total')</strong>
                            </div>
                            <div class="col-sm-6 col-xs-6 text-right">
                                <strong>{!! $currency.number_format((float)(!empty($order->calculate_total_price) ? $order->calculate_total_price : 0), 2, '.', '') !!}</strong>
                            </div>
                        </div>
                        @if($order->calculate_total_price <= $order->calculate_total_paid
                            || (empty($order->group_id) &&
                            $order->payment_method == \App\Models\SettingPayment::TYPE_MOLLIE &&
                            $order->status == \App\Models\Order::PAYMENT_STATUS_PAID))
                            <div class="row">
                                <div class="col-sm-12 col-xs-12 text-right">
                                    <strong>@lang('order.paid')</strong>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-sm-6 col-xs-6 text-right">
                                    @lang('order.paid')
                                </div>
                                <div class="col-sm-6 col-xs-6 text-right">
                                    {!! $currency.number_format((float)(!empty($order->calculate_total_paid) ? $order->calculate_total_paid : 0), 2, '.', '') !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 col-xs-6 text-right">
                                    @lang('order.user_paid')
                                </div>
                                <div class="col-sm-6 col-xs-6 text-right">
                                    {!! $currency.number_format((float)(!empty($order->calculate_total_price - $order->calculate_total_paid) ? $order->calculate_total_price - $order->calculate_total_paid : 0), 2, '.', '') !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>